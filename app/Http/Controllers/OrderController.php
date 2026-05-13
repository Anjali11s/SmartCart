<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;
use App\Mail\NewOrderForSellerMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Mail\ReturnRequestedMail;
use App\Mail\ReturnApprovedMail;
use App\Mail\ReturnRejectedMail;
use App\Mail\RefundProcessedMail;
use App\Mail\RefundProcessedSellerMail;

class OrderController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Display checkout page
     */
    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->cart;
        
        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        $cartItems = $cart->items()->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        
        // Check stock before checkout
        foreach ($cartItems as $item) {
            if ($item->product->quantity < $item->quantity) {
                return redirect()->route('cart.index')->with('error', 
                    "{$item->product->name} has only {$item->product->quantity} units in stock. Please update quantity.");
            }
        }
        
        // Check budget with monthly spent (only delivered orders)
        if ($user->budget) {
            $monthlySpent = Order::getMonthlySpent($user->id);
            $remainingBudget = $user->budget->amount - $monthlySpent;
            
            if ($total > $remainingBudget) {
                return redirect()->route('cart.index')->with('error', 
                    "❌ Cannot proceed to checkout! Your cart total (₹" . number_format($total) . 
                    ") exceeds your remaining monthly budget (₹" . number_format(max(0, $remainingBudget)) . 
                    "). You have already spent ₹" . number_format($monthlySpent) . " this month on delivered orders.");
            }
        }
        
        $addresses = $user->shippingAddresses;
        $defaultAddress = $addresses->where('is_default', true)->first();
        
        return view('orders.checkout', compact('cartItems', 'total', 'addresses', 'defaultAddress'));
    }

    /**
     * Place order
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:shipping_addresses,id',
            'payment_method' => 'required|in:COD,QR',
        ]);

        $user = Auth::user();
        $cart = $user->cart;
        
        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        $address = ShippingAddress::findOrFail($request->address_id);
        
        if ($address->user_id != $user->id) {
            abort(403);
        }

        DB::beginTransaction();
        
        try {
            $cartItems = $cart->items()->with('product')->get();
            $total = 0;
            
            foreach ($cartItems as $item) {
                if ($item->product->quantity < $item->quantity) {
                    throw new \Exception("{$item->product->name} is out of stock!");
                }
                $total += $item->product->price * $item->quantity;
            }
            
            // Check budget with monthly spent (only delivered orders)
            if ($user->budget) {
                $monthlySpent = Order::getMonthlySpent($user->id);
                $newTotalSpent = $monthlySpent + $total;
                
                if ($newTotalSpent > $user->budget->amount) {
                    $remaining = $user->budget->amount - $monthlySpent;
                    throw new \Exception("This order would exceed your monthly budget! Your remaining budget is ₹" . number_format(max(0, $remaining)));
                }
            }
            
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'order_status' => Order::STATUS_PENDING,
                'payment_status' => $request->payment_method == 'COD' ? 'pending' : 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address_id' => $address->id,
                'shipping_phone' => $address->phone,
                'shipping_address_text' => $address->full_address,
                'transaction_id' => $request->payment_method == 'QR' ? 'QR_' . uniqid() : null,
            ]);
            
            // Calculate and update seller earnings (assuming 90% to seller, 10% platform commission)
            $sellerEarnings = 0;
            foreach ($cartItems as $item) {
                $sellerEarnings += ($item->product->price * $item->quantity) * 0.90;
                
                // Create seller transaction record
                if ($item->product->seller_id) {
                    \DB::table('seller_transactions')->insert([
                        'seller_id' => $item->product->seller_id,
                        'order_id' => $order->id,
                        'amount' => ($item->product->price * $item->quantity) * 0.90,
                        'type' => 'earning',
                        'description' => 'Earning from order #' . $order->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            $order->update(['seller_earnings' => $sellerEarnings]);
            
            // Create order items and update stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);
                
                $item->product->decrement('quantity', $item->quantity);
            }
            
            $cart->items()->delete();
            
            DB::commit();

            // Send emails
            try {
                Mail::to($user->email)->send(new OrderPlacedMail($order));
            } catch (\Exception $e) {
                \Log::error("Failed to send order email: " . $e->getMessage());
            }

            try {
                $sellerEmails = [];
                foreach ($cartItems as $item) {
                    $seller = $item->product->seller;
                    if ($seller && $seller->email && !in_array($seller->email, $sellerEmails)) {
                        $sellerEmails[] = $seller->email;
                    }
                }
                foreach ($sellerEmails as $sellerEmail) {
                    Mail::to($sellerEmail)->send(new NewOrderForSellerMail($order));
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send seller emails: " . $e->getMessage());
            }
            
            if ($request->payment_method == 'QR') {
                return redirect()->route('orders.payment', $order)->with('success', 'Order placed! Please complete payment.');
            }
            
            return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display order list
     */
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product')->latest()->paginate(10);
        
        return view('orders.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        if ($order->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $order->load('items.product', 'shippingAddress');
        $timeline = $this->getOrderTimeline($order);
        
        return view('orders.show', compact('order', 'timeline'));
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled anymore.');
        }
        
        $request->validate([
            'cancellation_reason' => 'required|string|min:5'
        ]);
        
        DB::beginTransaction();
        
        try {
            foreach ($order->items as $item) {
                $item->product->increment('quantity', $item->quantity);
            }
            
            // Deduct seller earnings for cancelled order
            \DB::table('seller_transactions')->insert([
                'seller_id' => $order->items->first()->product->seller_id,
                'order_id' => $order->id,
                'amount' => -($order->seller_earnings),
                'type' => 'cancellation',
                'description' => 'Cancellation deduction for order #' . $order->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $order->update([
                'order_status' => Order::STATUS_CANCELLED,
                'cancellation_reason' => $request->cancellation_reason,
                'cancelled_at' => now(),
                'seller_earnings' => 0
            ]);
            
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Order cancelled successfully. Refund will be processed within 5-7 business days.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Request return
     */
    public function requestReturn(Request $request, Order $order)
    {
        if ($order->user_id != Auth::id()) {
            abort(403);
        }
        
        if (!$order->canRequestReturn()) {
            return back()->with('error', 'Return request cannot be placed for this order.');
        }
        
        $request->validate([
            'return_reason' => 'required|string|min:10'
        ]);
        
        $order->update([
            'order_status' => Order::STATUS_RETURN_REQUESTED,
            'return_reason' => $request->return_reason,
            'return_requested_at' => now()
        ]);
        
        // Send email to seller about return request
        try {
            $sellerEmails = [];
            foreach ($order->items as $item) {
                $seller = $item->product->seller;
                if ($seller && $seller->email && !in_array($seller->email, $sellerEmails)) {
                    $sellerEmails[] = $seller->email;
                }
            }
            foreach ($sellerEmails as $sellerEmail) {
                Mail::to($sellerEmail)->send(new ReturnRequestedMail($order));
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send return request email to seller: " . $e->getMessage());
        }
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Return request submitted successfully. We will review and get back to you within 2-3 business days.');
    }

    /**
     * Seller: Approve return request
     */
    public function approveReturn(Request $request, Order $order)
    {
        if (!Auth::user()->isSeller() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        if (!$order->canProcessReturn()) {
            return back()->with('error', 'This return request cannot be processed.');
        }
        
        DB::beginTransaction();
        
        try {
            // Restore stock
            foreach ($order->items as $item) {
                $item->product->increment('quantity', $item->quantity);
            }
            
            $order->update([
                'order_status' => Order::STATUS_RETURN_APPROVED,
                'return_approved_at' => now(),
                'refund_status' => Order::REFUND_PROCESSING,
                'refund_amount' => $order->total_amount
            ]);
            
            DB::commit();
            
            try {
                Mail::to($order->user->email)->send(new ReturnApprovedMail($order));
            } catch (\Exception $e) {
                \Log::error("Failed to send return approval email: " . $e->getMessage());
            }
            
            return back()->with('success', 'Return request approved. Refund will be processed shortly.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Seller: Reject return request
     */
    public function rejectReturn(Request $request, Order $order)
    {
        if (!Auth::user()->isSeller() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);
        
        $order->update([
            'order_status' => Order::STATUS_RETURN_REJECTED,
            'cancellation_reason' => $request->rejection_reason
        ]);
        
        try {
            Mail::to($order->user->email)->send(new ReturnRejectedMail($order, $request->rejection_reason));
        } catch (\Exception $e) {
            \Log::error("Failed to send return rejection email: " . $e->getMessage());
        }
        
        return back()->with('success', 'Return request rejected.');
    }

    /**
     * Seller: Process refund
     */
    public function processRefund(Order $order)
    {
        if (!Auth::user()->isSeller() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        if ($order->order_status != Order::STATUS_RETURN_APPROVED) {
            return back()->with('error', 'Refund can only be processed for approved returns.');
        }
        
        DB::beginTransaction();
        
        try {
            // Deduct from seller earnings
            foreach ($order->items as $item) {
                $seller = $item->product->seller;
                if ($seller) {
                    \DB::table('seller_transactions')->insert([
                        'seller_id' => $seller->id,
                        'order_id' => $order->id,
                        'amount' => -($item->price * $item->quantity * 0.90),
                        'type' => 'refund',
                        'description' => 'Refund deduction for order #' . $order->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            $order->update([
                'refund_status' => Order::REFUND_COMPLETED,
                'order_status' => Order::STATUS_RETURNED,
                'refund_processed_at' => now(),
                'seller_earnings' => 0
            ]);
            
            DB::commit();
            
            // Send refund confirmation email to user
            try {
                Mail::to($order->user->email)->send(new RefundProcessedMail($order));
            } catch (\Exception $e) {
                \Log::error("Failed to send refund email: " . $e->getMessage());
            }
            
            // Send notification to seller
            try {
                $sellerEmails = [];
                foreach ($order->items as $item) {
                    $seller = $item->product->seller;
                    if ($seller && $seller->email && !in_array($seller->email, $sellerEmails)) {
                        $sellerEmails[] = $seller->email;
                    }
                }
                foreach ($sellerEmails as $sellerEmail) {
                    Mail::to($sellerEmail)->send(new RefundProcessedSellerMail($order));
                }
            } catch (\Exception $e) {
                \Log::error("Failed to send refund notification to seller: " . $e->getMessage());
            }
            
            return back()->with('success', 'Refund processed successfully! Amount will be credited to customer within 5-7 business days.');
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Payment page (for QR payments)
     */
    public function payment(Order $order)
    {
        if ($order->user_id != Auth::id()) {
            abort(403);
        }
        
        return view('orders.payment', compact('order'));
    }

    /**
     * Upload payment proof (for QR payments)
     */
    public function uploadPaymentProof(Request $request, Order $order)
    {
        $request->validate([
            'transaction_id' => 'required|string',
            'payment_screenshot' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $path = $request->file('payment_screenshot')->store('payments', 'public');
        
        $order->update([
            'transaction_id' => $request->transaction_id,
            'payment_status' => Order::PAYMENT_SUBMITTED,
            'payment_screenshot' => $path
        ]);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Payment proof submitted. Our team will verify it shortly.');
    }

    /**
     * Track order
     */
    public function track(Order $order)
    {
        if ($order->user_id != Auth::id()) {
            abort(403);
        }
        
        return view('orders.track', compact('order'));
    }

    /**
     * Seller: View all orders (for products they sell)
     */
    public function sellerOrders(Request $request)
    {
        if (!Auth::user()->isSeller()) {
            abort(403);
        }
        
        $query = Order::whereHas('items.product', function($q) {
            $q->where('seller_id', Auth::id());
        })->with('user', 'items.product');
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status != 'all' && $request->status != '') {
            $query->where('order_status', $request->status);
        }
        
        $orders = $query->latest()->paginate(20);
        
        $stats = [
            'pending' => Order::whereHas('items.product', function($q) {
                $q->where('seller_id', Auth::id());
            })->where('order_status', Order::STATUS_PENDING)->count(),
            'processing' => Order::whereHas('items.product', function($q) {
                $q->where('seller_id', Auth::id());
            })->where('order_status', Order::STATUS_PROCESSING)->count(),
            'shipped' => Order::whereHas('items.product', function($q) {
                $q->where('seller_id', Auth::id());
            })->where('order_status', Order::STATUS_SHIPPED)->count(),
            'delivered' => Order::whereHas('items.product', function($q) {
                $q->where('seller_id', Auth::id());
            })->where('order_status', Order::STATUS_DELIVERED)->count(),
            'return_requested' => Order::whereHas('items.product', function($q) {
                $q->where('seller_id', Auth::id());
            })->where('order_status', Order::STATUS_RETURN_REQUESTED)->count(),
        ];
        
        return view('seller.orders', compact('orders', 'stats'));
    }

    /**
     * Seller: View single order details
     */
    public function sellerOrderShow(Order $order)
    {
        if (!Auth::user()->isSeller()) {
            abort(403);
        }
        
        $hasSellerProducts = $order->items()->whereHas('product', function($q) {
            $q->where('seller_id', Auth::id());
        })->exists();
        
        if (!$hasSellerProducts) {
            abort(403);
        }
        
        $order->load('user', 'items.product', 'shippingAddress');
        
        return view('seller.order-show', compact('order'));
    }

    /**
     * Seller: Update order status
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        if (!Auth::user()->isSeller()) {
            abort(403);
        }
        
        // Check if order can be updated (not delivered, cancelled, or returned)
        $cannotUpdate = in_array($order->order_status, [
            Order::STATUS_DELIVERED,
            Order::STATUS_CANCELLED,
            Order::STATUS_RETURNED,
            Order::STATUS_RETURN_APPROVED,
            Order::STATUS_RETURN_REJECTED
        ]);
        
        if ($cannotUpdate) {
            return back()->with('error', 'This order cannot be updated anymore as it is ' . str_replace('_', ' ', $order->order_status));
        }
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,out_for_delivery,delivered',
            'tracking_number' => 'nullable|string',
            'courier_name' => 'nullable|string'
        ]);
        
        // Don't allow changing status back from delivered
        if ($order->order_status == Order::STATUS_DELIVERED && $request->status != Order::STATUS_DELIVERED) {
            return back()->with('error', 'Delivered orders cannot be updated.');
        }
        
        $order->update([
            'order_status' => $request->status,
            'tracking_number' => $request->tracking_number ?? $order->tracking_number,
            'courier_name' => $request->courier_name ?? $order->courier_name,
            'shipped_at' => $request->status == Order::STATUS_SHIPPED ? now() : $order->shipped_at,
            'delivered_at' => $request->status == Order::STATUS_DELIVERED ? now() : null,
        ]);
        
        try {
            Mail::to($order->user->email)->send(new OrderStatusUpdatedMail($order));
        } catch (\Exception $e) {
            \Log::error("Failed to send status update email: " . $e->getMessage());
        }
        
        return back()->with('success', 'Order status updated successfully!');
    }

    // Helper Methods
    private function getOrderTimeline($order)
    {
        $timeline = [
            ['status' => 'Order Placed', 'date' => $order->created_at, 'completed' => true, 'icon' => 'fa-shopping-cart']
        ];
        
        if ($order->order_status != Order::STATUS_PENDING && $order->order_status != Order::STATUS_CANCELLED) {
            $timeline[] = [
                'status' => 'Order Confirmed',
                'date' => $order->created_at->addHours(2),
                'completed' => in_array($order->order_status, [Order::STATUS_CONFIRMED, Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED]),
                'icon' => 'fa-check-circle'
            ];
        }
        
        if (in_array($order->order_status, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED])) {
            $timeline[] = [
                'status' => 'Processing',
                'date' => $order->created_at->addHours(24),
                'completed' => in_array($order->order_status, [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED]),
                'icon' => 'fa-cogs'
            ];
        }
        
        if (in_array($order->order_status, [Order::STATUS_SHIPPED, Order::STATUS_OUT_FOR_DELIVERY, Order::STATUS_DELIVERED])) {
            $timeline[] = [
                'status' => 'Shipped',
                'date' => $order->shipped_at ?? $order->updated_at,
                'completed' => $order->order_status != Order::STATUS_PROCESSING && $order->order_status != Order::STATUS_CONFIRMED,
                'icon' => 'fa-shipping-fast'
            ];
        }
        
        if ($order->order_status == Order::STATUS_DELIVERED) {
            $timeline[] = [
                'status' => 'Delivered',
                'date' => $order->delivered_at,
                'completed' => true,
                'icon' => 'fa-check-double'
            ];
        }
        
        return $timeline;
    }
}