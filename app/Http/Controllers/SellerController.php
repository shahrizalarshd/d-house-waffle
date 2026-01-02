<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Exports\OrdersExport;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SellerController extends Controller
{
    public function dashboard()
    {
        $totalOrders = Order::where('seller_id', auth()->id())->count();
        $pendingOrders = Order::where('seller_id', auth()->id())
            ->where('status', 'pending')
            ->count();
        $totalEarnings = Order::where('seller_id', auth()->id())
            ->where('payment_status', 'paid')
            ->sum('seller_amount');
        $activeProducts = Product::where('seller_id', auth()->id())
            ->where('is_active', true)
            ->count();

        $recentOrders = Order::with(['buyer', 'items'])
            ->where('seller_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'totalEarnings',
            'activeProducts',
            'recentOrders'
        ));
    }

    public function orders()
    {
        $orders = Order::with(['buyer', 'items.product'])
            ->where('seller_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('seller.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        $order = Order::with('buyer')->where('seller_id', auth()->id())->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = $request->status;
        
        $order->update(['status' => $newStatus]);

        // Award loyalty stamp when order is completed (only for registered users)
        if ($newStatus === 'completed' && $oldStatus !== 'completed' && $order->buyer_id) {
            $loyaltyService = app(LoyaltyService::class);
            $loyaltyService->awardStamp($order->buyer, $order);
        }

        // Reverse stamp if order is cancelled within same day (for registered users)
        if ($newStatus === 'cancelled' && $oldStatus === 'completed' && $order->buyer_id) {
            if ($order->updated_at->isToday()) {
                $loyaltyService = app(LoyaltyService::class);
                $loyaltyService->reverseStamp($order->buyer, $order);
            }
        }

        return back()->with('success', 'Order status updated successfully');
    }

    public function products()
    {
        $products = Product::where('seller_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('seller.products', compact('products'));
    }

    public function markAsPaid(Request $request, $id)
    {
        $order = Order::with('buyer')
            ->where('seller_id', auth()->id())
            ->findOrFail($id);

        // Verify it's cash payment
        if ($order->payment_method !== 'cash') {
            return back()->with('error', 'Only cash orders can be marked as paid manually.');
        }

        // Verify not already paid
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Order already marked as paid.');
        }

        $oldStatus = $order->status;

        // Update order
        $order->update([
            'payment_status' => 'paid',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // Award loyalty stamp (only for registered users)
        if ($oldStatus !== 'completed' && $order->buyer_id) {
            $loyaltyService = app(LoyaltyService::class);
            $loyaltyService->awardStamp($order->buyer, $order);
        }

        // Log the confirmation
        \Log::info('Cash payment confirmed', [
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'seller_id' => auth()->id(),
            'amount' => $order->total_amount,
            'is_guest' => $order->isGuestOrder(),
        ]);

        return back()->with('success', 'Cash payment confirmed! Order completed.');
    }

    public function verifyQrPayment(Request $request, $id)
    {
        $order = Order::with('buyer')
            ->where('seller_id', auth()->id())
            ->findOrFail($id);

        // Verify it's QR payment
        if ($order->payment_method !== 'qr') {
            return back()->with('error', 'Only QR orders can be verified.');
        }

        // Verify not already paid
        if ($order->payment_status === 'paid') {
            return back()->with('error', 'Order already verified.');
        }

        // Verify payment proof exists
        if (!$order->hasPaymentProof()) {
            return back()->with('error', 'Buyer has not uploaded payment proof yet.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        if ($request->action === 'approve') {
            $oldStatus = $order->status;

            $order->update([
                'payment_status' => 'paid',
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            // Award loyalty stamp (only for registered users)
            if ($oldStatus !== 'completed' && $order->buyer_id) {
                $loyaltyService = app(LoyaltyService::class);
                $loyaltyService->awardStamp($order->buyer, $order);
            }

            // Log the verification
            \Log::info('QR payment verified', [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'seller_id' => auth()->id(),
                'buyer_id' => $order->buyer_id,
                'amount' => $order->total_amount,
                'is_guest' => $order->isGuestOrder(),
            ]);

            return back()->with('success', 'Payment verified! Order completed.');
        } else {
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled',
            ]);

            return back()->with('error', 'Payment rejected. Order cancelled.');
        }
    }

    public function profile()
    {
        $seller = auth()->user();
        return view('seller.profile', compact('seller'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'unit_no' => 'nullable|string|max:50',
            'block' => 'nullable|string|max:50',
            'apartment_id' => 'nullable|exists:apartments,id',
            'qr_code_image' => 'nullable|image|max:2048',
            'qr_code_type' => 'nullable|string|max:50',
            'qr_code_instructions' => 'nullable|string|max:500',
        ]);

        $seller = auth()->user();

        // Update basic profile info if provided
        if ($request->has('name')) {
            $seller->name = $validated['name'];
        }
        if ($request->has('phone')) {
            $seller->phone = $validated['phone'];
        }
        if ($request->has('unit_no')) {
            $seller->unit_no = $validated['unit_no'];
        }
        if ($request->has('block')) {
            $seller->block = $validated['block'];
        }
        if ($request->has('apartment_id')) {
            $seller->apartment_id = $validated['apartment_id'];
        }

        // Update QR code if uploaded
        if ($request->hasFile('qr_code_image')) {
            // Delete old QR code if exists
            if ($seller->qr_code_image) {
                \Storage::disk('public')->delete($seller->qr_code_image);
            }

            $path = $request->file('qr_code_image')->store('qr-codes', 'public');
            $seller->qr_code_image = $path;
        }

        $seller->qr_code_type = $validated['qr_code_type'] ?? null;
        $seller->qr_code_instructions = $validated['qr_code_instructions'] ?? null;
        $seller->save();

        return back()->with('success', 'QR payment settings updated successfully!');
    }

    public function salesReport(Request $request)
    {
        // Build query with filters
        $query = Order::with(['buyer', 'items'])
            ->where('seller_id', auth()->id());

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Payment status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Get orders
        $orders = $query->latest()->paginate(20);

        // Calculate stats
        $statsQuery = Order::where('seller_id', auth()->id());
        
        if ($request->filled('date_from')) {
            $statsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $statsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $statsQuery->where('payment_method', $request->payment_method);
        }
        if ($request->filled('payment_status')) {
            $statsQuery->where('payment_status', $request->payment_status);
        }

        $stats = [
            'total_orders' => $statsQuery->count(),
            'total_revenue' => $statsQuery->where('payment_status', 'paid')->sum('seller_amount'),
            'avg_order_value' => $statsQuery->where('payment_status', 'paid')->avg('seller_amount') ?? 0,
            'total_items' => \DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.seller_id', auth()->id())
                ->when($request->filled('date_from'), fn($q) => $q->whereDate('orders.created_at', '>=', $request->date_from))
                ->when($request->filled('date_to'), fn($q) => $q->whereDate('orders.created_at', '<=', $request->date_to))
                ->sum('order_items.quantity'),
        ];

        return view('owner.sales-report', compact('orders', 'stats'));
    }

    public function exportSalesReport(Request $request)
    {
        // Build query with same filters
        $query = Order::with(['buyer', 'items'])
            ->where('seller_id', auth()->id());

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->get();

        $filename = 'dhouse-waffle-sales-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new OrdersExport($orders), $filename);
    }
}
