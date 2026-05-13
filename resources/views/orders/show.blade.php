@extends('layouts.master')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 text-white">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <div>
                <h1 class="text-xl font-bold">Order #{{ $order->id }}</h1>
                <p class="text-sm opacity-90">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="px-3 py-1 rounded-full text-sm font-semibold bg-white/20 backdrop-blur">
                <i class="fas {{ $order->status_icon }}"></i> {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
            </div>
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="font-semibold mb-2"><i class="fas fa-map-marker-alt text-indigo-600"></i> Shipping Address</h3>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="font-medium">{{ $order->shippingAddress?->full_name ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shippingAddress?->full_address ?? $order->shipping_address_text }}</p>
                    <p class="text-sm text-gray-600">Phone: {{ $order->shipping_phone }}</p>
                </div>
            </div>
            <div>
                <h3 class="font-semibold mb-2"><i class="fas fa-credit-card text-indigo-600"></i> Payment Info</h3>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p>Method: <strong>{{ $order->payment_method == 'COD' ? 'Cash on Delivery' : 'Online Payment' }}</strong></p>
                    <p>Status: <span class="px-2 py-1 rounded-full text-xs 
                        @if($order->payment_status == 'verified') bg-green-100 text-green-800
                        @elseif($order->payment_status == 'submitted') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($order->payment_status) }}
                    </span></p>
                    @if($order->transaction_id)
                        <p>Transaction ID: <span class="font-mono text-sm">{{ $order->transaction_id }}</span></p>
                    @endif
                </div>
            </div>
        </div>
        
        <h3 class="font-semibold mb-3"><i class="fas fa-box text-indigo-600"></i> Order Items</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-center">Quantity</th>
                        <th class="p-3 text-right">Price</th>
                        <th class="p-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr class="border-b">
                        <td class="p-3">{{ $item->product->name }}</td>
                        <td class="p-3 text-center">{{ $item->quantity }}</td>
                        <td class="p-3 text-right">₹{{ number_format($item->price, 2) }}</td>
                        <td class="p-3 text-right font-semibold">₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="p-3 text-right font-bold">Total Amount:</td>
                        <td class="p-3 text-right font-bold text-indigo-600">₹{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        @if($order->canBeCancelled())
        <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
            <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                @csrf
                <textarea name="cancellation_reason" class="w-full p-2 border rounded-lg mb-2" placeholder="Reason for cancellation" rows="2" required></textarea>
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">Cancel Order</button>
            </form>
        </div>
        @endif
        
        @if($order->canRequestReturn())
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <form action="{{ route('orders.return', $order) }}" method="POST" onsubmit="return confirm('Request return for this order?')">
                @csrf
                <textarea name="return_reason" class="w-full p-2 border rounded-lg mb-2" placeholder="Reason for return" rows="2" required></textarea>
                <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">Request Return</button>
            </form>
        </div>
        @endif
        
        <div class="mt-6 flex gap-3">
            <a href="{{ route('orders.track', $order) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">Track Order</a>
            @if($order->payment_method == 'QR' && $order->payment_status == 'pending')
                <a href="{{ route('orders.payment', $order) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">Make Payment</a>
            @endif
        </div>
    </div>
</div>
@endsection