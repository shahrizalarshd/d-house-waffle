<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\User;
use App\Events\OrderPlaced;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function checkout()
    {
        $apartment = Apartment::find(auth()->user()->apartment_id);
        return view('buyer.checkout', compact('apartment'));
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'cart' => 'required|array',
            'cart.*.product_id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:online,cash,qr',
        ]);

        // Group cart items by seller
        $cart = collect($validated['cart']);
        $products = Product::whereIn('id', $cart->pluck('product_id'))->get();
        
        $groupedBySeller = $cart->groupBy(function ($item) use ($products) {
            return $products->firstWhere('id', $item['product_id'])->seller_id;
        });

        $orders = [];

        foreach ($groupedBySeller as $sellerId => $items) {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($items as $item) {
                $product = $products->firstWhere('id', $item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
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

            $apartment = auth()->user()->apartment;
            $platformFee = $totalAmount * ($apartment->service_fee_percent / 100);
            $sellerAmount = $totalAmount - $platformFee;

            // Parse pickup time (format: HH:MM:SS)
            $pickupTime = explode(':', $apartment->pickup_start_time);
            
            $order = Order::create([
                'apartment_id' => auth()->user()->apartment_id,
                'buyer_id' => auth()->id(),
                'seller_id' => $sellerId,
                'order_no' => 'ORD-' . strtoupper(Str::random(10)),
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
            ]);

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

            // Broadcast order placed event for real-time notifications
            broadcast(new OrderPlaced($order->load('buyer')))->toOthers();

            $orders[] = $order;
        }

        // Return JSON response for AJAX or redirect
        if ($request->expectsJson()) {
            // For online payment, redirect to payment page
            if ($validated['payment_method'] === 'online' && count($orders) === 1) {
                $redirect = route('payment.show', $orders[0]->id);
                $message = 'Order placed! Please complete payment.';
            } elseif ($validated['payment_method'] === 'qr' && count($orders) === 1) {
                $redirect = route('orders.qr-payment', $orders[0]->id);
                $message = 'Order placed! Please scan QR code to pay.';
            } else {
                $redirect = route('buyer.orders');
                $message = $validated['payment_method'] === 'cash' 
                    ? 'Order placed! Pay cash to seller at pickup.'
                    : 'Orders placed successfully';
            }
            
            return response()->json([
                'success' => true,
                'redirect' => $redirect,
                'message' => $message
            ]);
        }

        // Redirect based on payment method
        if ($validated['payment_method'] === 'online' && count($orders) === 1) {
            return redirect()->route('payment.show', $orders[0]->id);
        }

        if ($validated['payment_method'] === 'qr' && count($orders) === 1) {
            return redirect()->route('orders.qr-payment', $orders[0]->id);
        }

        $message = $validated['payment_method'] === 'cash' 
            ? 'Order placed! Pay cash to seller at pickup.'
            : 'Orders placed successfully';
            
        return redirect()->route('buyer.orders')->with('success', $message);
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
        $order = Order::with(['seller', 'items'])
            ->where('buyer_id', auth()->id())
            ->findOrFail($id);

        if ($order->payment_method !== 'qr') {
            return redirect()->route('buyer.order.detail', $id);
        }

        return view('buyer.qr-payment', compact('order'));
    }

    public function uploadPaymentProof(Request $request, $id)
    {
        $order = Order::where('buyer_id', auth()->id())
            ->findOrFail($id);

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

        return redirect()->route('buyer.order.detail', $order->id)
            ->with('success', 'Payment proof uploaded! Waiting for seller verification.');
    }
}
