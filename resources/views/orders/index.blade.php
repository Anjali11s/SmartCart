@extends('layouts.master')

@section('title', 'My Orders')

@section('content')
<h1 class="text-2xl font-bold mb-6"><i class="fas fa-box"></i> My Orders</h1>

@if($orders->count() > 0)
    @foreach($orders as $order)
    <div class="bg-white rounded-xl shadow-sm mb-4 overflow-hidden">
        <div class="bg-gray-50 p-4 border-b flex justify-between items-center flex-wrap gap-3">
            <div>
                <span class="text-gray-500 text-sm">Order #{{ $order->id }}</span>
                <div class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, h:i A') }}</div>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $order->status_badge_class }}">
                    <i class="fas {{ $order->status_icon }}"></i> {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                </span>
            </div>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($order->items->take(2) as $item)
                <div class="flex gap-3 items-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-gray-400"></i>
                    </div>
                    <div>
                        <div class="font-medium">{{ $item->product->name }}</div>
                        <div class="text-sm text-gray-500">Qty: {{ $item->quantity }} × ₹{{ number_format($item->price, 2) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($order->items->count() > 2)
                <div class="text-sm text-gray-500 mt-2">+{{ $order->items->count() - 2 }} more items</div>
            @endif
        </div>
        <div class="bg-gray-50 p-4 border-t flex justify-between items-center flex-wrap gap-3">
            <div>
                <span class="text-gray-500">Total Amount:</span>
                <span class="font-bold text-indigo-600 text-lg ml-2">₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('orders.show', $order) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 transition">View Details</a>
                <a href="{{ route('orders.track', $order) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition">Track Order</a>
            </div>
        </div>
    </div>
    @endforeach
    
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
@else
    <div class="bg-white rounded-xl p-12 text-center">
        <i class="fas fa-box-open text-5xl text-gray-300"></i>
        <h3 class="text-xl font-semibold text-gray-700 mt-4">No Orders Yet</h3>
        <p class="text-gray-500 mt-2">Start shopping to see your orders here!</p>
        <a href="{{ route('products.index') }}" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg">Browse Products</a>
    </div>
@endif
@endsection