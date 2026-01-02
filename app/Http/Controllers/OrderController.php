<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\User;
use App\Models\LoyaltySetting;
use App\Events\OrderPlaced;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Checkout page - works for both guests and logged in users
     */
    public function checkout()
    {
        // Get apartment (from user or default)
        if (auth()->check()) {
            $apartment = Apartment::find(auth()->user()->apartment_id);
            $loyaltyService = app(LoyaltyService::class);
            $loyaltySummary = $loyaltyService->getLoyaltySummary(auth()->user());
            $discountInfo = null; // Will be calculated on frontend
        } else {
            $apartment = Apartment::first();
            $loyaltySummary = null;
            $discountInfo = null;
        }
        
        $loyaltySettings = $apartment ? LoyaltySetting::getForApartment($apartment->id) : null;
        
        return view('buyer.checkout', compact('apartment', 'loyaltySummary', 'loyaltySettings'));
    }

    /**
     * Place order - supports guest checkout and loyalty discounts
     */
    public function placeOrder(Request $request)
    {
        // Base validation
        $rules = [
            'cart' => 'required|array|min:1',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:online,cash,qr',
        ];

        // Guest checkout validation
        $isGuest = !auth()->check();
        if ($isGuest) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_phone'] = 'required|string|max:20';
            $rules['guest_block'] = 'required|string|max:10';
            $rules['guest_unit_no'] = 'required|string|max:20';
        }

        $validated = $request->validate($rules);

        // Get apartment
        if ($isGuest) {
            $apartment = Apartment::first();
            $buyerId = null;
        } else {
            $apartment = auth()->user()->apartment;
            $buyerId = auth()->id();
        }

        // Check guest pending orders limit
        if ($isGuest) {
            $loyaltySettings = LoyaltySetting::getForApartment($apartment->id);
            $pendingGuestOrders = Order::where('guest_phone', $validated['guest_phone'])
                ->whereIn('status', ['pending', 'preparing'])
                ->count();
            
            if ($pendingGuestOrders >= $loyaltySettings->guest_pending_limit) {
                $message = "You have {$pendingGuestOrders} pending orders. Please wait for them to complete before placing new orders.";
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return back()->with('error', $message);
            }
        }

        // Group cart items by seller
        $cart = collect($validated['cart']);
        $products = Product::whereIn('id', $cart->pluck('product_id'))->get();
        
        $groupedBySeller = $cart->groupBy(function ($item) use ($products) {
            return $products->firstWhere('id', $item['product_id'])->seller_id;
        });

        $orders = [];

        foreach ($groupedBySeller as $sellerId => $items) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $product = $products->firstWhere('id', $item['product_id']);
                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Check if seller accepts QR payment
            if ($validated['payment_method'] === 'qr') {
                $seller = User::find($sellerId);
                if (!$seller->hasQRCode()) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Seller {$seller->name} does not accept QR payments yet."
                        ], 400);
                    }
                    return back()->with('error', "Seller {$seller->name} does not accept QR payments yet.");
                }
            }

            // Calculate loyalty discount (only for registered users)
            $discountAmount = 0;
            $discountType = null;
            $discountDetails = null;
            
            if (!$isGuest) {
                $loyaltyService = app(LoyaltyService::class);
                $discountResult = $loyaltyService->calculateDiscount(auth()->user(), $subtotal);
                
                if ($discountResult['has_discount']) {
                    $discountAmount = $discountResult['total_discount'];
                    $discountType = 'loyalty';
                    $discountDetails = $discountResult['details'];
                }
            }

            // Calculate totals
            $totalAmount = $subtotal - $discountAmount;
            $platformFee = $totalAmount * ($apartment->service_fee_percent / 100);
            $sellerAmount = $totalAmount - $platformFee;

            // Parse pickup time (format: HH:MM:SS)
            $pickupTime = explode(':', $apartment->pickup_start_time);
            
            // Prepare order data
            $orderData = [
                'apartment_id' => $apartment->id,
                'buyer_id' => $buyerId,
                'seller_id' => $sellerId,
                'order_no' => 'ORD-' . strtoupper(Str::random(10)),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_type' => $discountType,
                'discount_details' => $discountDetails,
                'total_amount' => $totalAmount,
                'platform_fee' => $platformFee,
                'seller_amount' => $sellerAmount,
                'status' => 'pending',
                'pickup_location' => $apartment->pickup_location,
                'pickup_time' => now()->addDay()->setTime(
                    (int) $pickupTime[0],
                    (int) $pickupTime[1]
                ),
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
            ];

            // Add guest info if guest checkout
            if ($isGuest) {
                $orderData['guest_name'] = $validated['guest_name'];
                $orderData['guest_phone'] = $validated['guest_phone'];
                $orderData['guest_block'] = $validated['guest_block'];
                $orderData['guest_unit_no'] = $validated['guest_unit_no'];
                $orderData['guest_token'] = Str::random(32);
            }

            $order = Order::create($orderData);

            foreach ($orderItems as $item) {
                OrderItem::create(array_merge(['order_id' => $order->id], $item));
            }

            // Create payment record only for online payments
            if ($validated['payment_method'] === 'online') {
                Payment::create([
                    'order_id' => $order->id,
                    'gateway' => 'billplz', // Default gateway
                    'amount' => $totalAmount,
                    'status' => 'pending',
                ]);
            }

            // Mark loyalty discount as used (for registered users)
            if (!$isGuest && $discountAmount > 0) {
                $loyaltyService->useDiscount(auth()->user(), $order);
            }

            // Broadcast order placed event for real-time notifications
            broadcast(new OrderPlaced($order->load('buyer')))->toOthers();

            $orders[] = $order;
        }

        // Prepare response
        $firstOrder = $orders[0];
        
        if ($request->expectsJson()) {
            if ($validated['payment_method'] === 'online' && count($orders) === 1) {
                $redirect = route('payment.show', $firstOrder->id);
                $message = 'Order placed! Please complete payment.';
            } elseif ($validated['payment_method'] === 'qr' && count($orders) === 1) {
                $redirect = route('orders.qr-payment', $firstOrder->id);
                $message = 'Order placed! Please scan QR code to pay.';
            } elseif ($isGuest) {
                // Guest: redirect to tracking page
                $redirect = route('order.track', $firstOrder->guest_token);
                $message = $validated['payment_method'] === 'cash' 
                    ? 'Order placed! Pay cash to seller at pickup.'
                    : 'Order placed successfully!';
            } else {
                $redirect = route('buyer.orders');
                $message = $validated['payment_method'] === 'cash' 
                    ? 'Order placed! Pay cash to seller at pickup.'
                    : 'Orders placed successfully';
            }
            
            return response()->json([
                'success' => true,
                'redirect' => $redirect,
                'message' => $message,
                'order_no' => $firstOrder->order_no,
                'guest_token' => $isGuest ? $firstOrder->guest_token : null,
            ]);
        }

        // Non-AJAX redirect
        if ($validated['payment_method'] === 'online' && count($orders) === 1) {
            return redirect()->route('payment.show', $firstOrder->id);
        }

        if ($validated['payment_method'] === 'qr' && count($orders) === 1) {
            return redirect()->route('orders.qr-payment', $firstOrder->id);
        }

        if ($isGuest) {
            return redirect()->route('order.track', $firstOrder->guest_token)
                ->with('success', 'Order placed successfully! Save this link to track your order.');
        }

        $message = $validated['payment_method'] === 'cash' 
            ? 'Order placed! Pay cash to seller at pickup.'
            : 'Orders placed successfully';
            
        return redirect()->route('buyer.orders')->with('success', $message);
    }

    /**
     * Track guest order by token
     */
    public function trackGuestOrder($token)
    {
        $order = Order::with(['seller', 'items.product'])
            ->byGuestToken($token)
            ->firstOrFail();

        return view('buyer.order-track', compact('order'));
    }

    public function showPayment($id)
    {
        $order = Order::with(['seller', 'items', 'payment'])
            ->where('buyer_id', auth()->id())
            ->findOrFail($id);

        return view('buyer.payment', compact('order'));
    }

    public function showQRPayment($id)
    {
        // Allow both guest and registered users to access QR payment
        $query = Order::with(['seller', 'items']);
        
        if (auth()->check()) {
            $query->where(function($q) use ($id) {
                $q->where('buyer_id', auth()->id())
                  ->orWhereNotNull('guest_token');
            });
        }
        
        $order = $query->findOrFail($id);

        if ($order->payment_method !== 'qr') {
            if ($order->isGuestOrder()) {
                return redirect()->route('order.track', $order->guest_token);
            }
            return redirect()->route('buyer.order.detail', $id);
        }

        return view('buyer.qr-payment', compact('order'));
    }

    public function uploadPaymentProof(Request $request, $id)
    {
        // Allow both guest and registered users
        $query = Order::query();
        
        if (auth()->check()) {
            $query->where(function($q) {
                $q->where('buyer_id', auth()->id())
                  ->orWhereNotNull('guest_token');
            });
        } else {
            $query->whereNotNull('guest_token');
        }
        
        $order = $query->findOrFail($id);

        if ($order->payment_method !== 'qr') {
            return back()->with('error', 'Only QR orders can upload payment proof.');
        }

        $validated = $request->validate([
            'payment_proof' => 'required|image|max:5120', // 5MB max
            'payment_notes' => 'nullable|string|max:500',
        ]);

        // Store payment proof image
        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $order->update([
            'payment_proof' => $path,
            'payment_notes' => $validated['payment_notes'] ?? null,
        ]);

        // Redirect based on guest or registered
        if ($order->isGuestOrder()) {
            return redirect()->route('order.track', $order->guest_token)
                ->with('success', 'Payment proof uploaded! Waiting for seller verification.');
        }

        return redirect()->route('buyer.order.detail', $order->id)
            ->with('success', 'Payment proof uploaded! Waiting for seller verification.');
    }
}
