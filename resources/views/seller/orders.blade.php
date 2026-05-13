@extends('layouts.master')

@section('title', 'Manage Orders')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold"><i class="fas fa-box"></i> Manage Orders</h1>
    <p class="text-gray-500">View and manage all orders for your products</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 text-center shadow-sm hover:shadow-md transition">
        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</div>
        <div class="text-sm text-gray-500">Pending</div>
    </div>
    <div class="bg-white rounded-xl p-4 text-center shadow-sm hover:shadow-md transition">
        <div class="text-2xl font-bold text-blue-600">{{ $stats['processing'] ?? 0 }}</div>
        <div class="text-sm text-gray-500">Processing</div>
    </div>
    <div class="bg-white rounded-xl p-4 text-center shadow-sm hover:shadow-md transition">
        <div class="text-2xl font-bold text-purple-600">{{ $stats['shipped'] ?? 0 }}</div>
        <div class="text-sm text-gray-500">Shipped</div>
    </div>
    <div class="bg-white rounded-xl p-4 text-center shadow-sm hover:shadow-md transition">
        <div class="text-2xl font-bold text-green-600">{{ $stats['delivered'] ?? 0 }}</div>
        <div class="text-sm text-gray-500">Delivered</div>
    </div>
    <div class="bg-white rounded-xl p-4 text-center shadow-sm hover:shadow-md transition">
        <div class="text-2xl font-bold text-orange-600">{{ $stats['return_requested'] ?? 0 }}</div>
        <div class="text-sm text-gray-500">Return Req.</div>
    </div>
    <div class="bg-white rounded-xl p-4 text-center shadow-sm hover:shadow-md transition">
        <div class="text-2xl font-bold text-gray-600">{{ array_sum($stats) }}</div>
        <div class="text-sm text-gray-500">Total</div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="flex flex-wrap gap-2 mb-4">
    <a href="{{ route('seller.orders') }}" class="px-3 py-1 rounded-full text-sm {{ !request()->get('status') ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">All</a>
    <a href="{{ route('seller.orders', ['status' => 'pending']) }}" class="px-3 py-1 rounded-full text-sm {{ request()->get('status') == 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Pending</a>
    <a href="{{ route('seller.orders', ['status' => 'processing']) }}" class="px-3 py-1 rounded-full text-sm {{ request()->get('status') == 'processing' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Processing</a>
    <a href="{{ route('seller.orders', ['status' => 'shipped']) }}" class="px-3 py-1 rounded-full text-sm {{ request()->get('status') == 'shipped' ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Shipped</a>
    <a href="{{ route('seller.orders', ['status' => 'delivered']) }}" class="px-3 py-1 rounded-full text-sm {{ request()->get('status') == 'delivered' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Delivered</a>
    <a href="{{ route('seller.orders', ['status' => 'return_requested']) }}" class="px-3 py-1 rounded-full text-sm {{ request()->get('status') == 'return_requested' ? 'bg-orange-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Return Requested</a>
    <a href="{{ route('seller.orders', ['status' => 'cancelled']) }}" class="px-3 py-1 rounded-full text-sm {{ request()->get('status') == 'cancelled' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Cancelled</a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Order ID</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Customer</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Date</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Items</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Total</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Status</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Payment</th>
                    <th class="p-3 text-left text-sm font-medium text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-3 font-medium text-indigo-600">#{{ $order->id }}</td>
                    <td class="p-3">
                        <div class="font-medium">{{ $order->user->name }}</div>
                        <div class="text-xs text-gray-400">{{ $order->user->email }}</div>
                    </div>
                    <td class="p-3 text-sm">
                        {{ $order->created_at->format('d M Y') }}
                        <div class="text-xs text-gray-400">{{ $order->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="p-3">
                        <div class="text-sm">
                            @foreach($order->items->take(2) as $item)
                                <div class="flex items-center gap-1">
                                    <span class="text-gray-600">{{ $item->quantity }}x</span>
                                    <span>{{ Str::limit($item->product->name, 25) }}</span>
                                    @if($item->product->seller_id == Auth::id())
                                        <span class="text-green-600 text-xs">(Yours)</span>
                                    @endif
                                </div>
                            @endforeach
                            @if($order->items->count() > 2)
                                <div class="text-xs text-gray-400 mt-1">+{{ $order->items->count() - 2 }} more items</div>
                            @endif
                        </div>
                    </td>
                    <td class="p-3 font-semibold text-indigo-600">₹{{ number_format($order->total_amount, 2) }}</td>
                    <td class="p-3">
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock'],
                                'confirmed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-check-circle'],
                                'processing' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-cogs'],
                                'shipped' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'fa-shipping-fast'],
                                'out_for_delivery' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'icon' => 'fa-truck'],
                                'delivered' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-double'],
                                'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle'],
                                'return_requested' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'fa-exchange-alt'],
                                'return_approved' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-check-circle'],
                                'return_rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle'],
                                'returned' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-undo-alt'],
                            ];
                            $config = $statusConfig[$order->order_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-box'];
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }}">
                            <i class="fas {{ $config['icon'] }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                        </span>
                        @if(in_array($order->order_status, ['delivered', 'cancelled', 'returned']))
                            <div class="text-xs text-gray-400 mt-1">(Final)</div>
                        @endif
                    </td>
                    <td class="p-3">
                        @if($order->payment_method == 'COD')
                            <span class="text-xs text-gray-600">Cash on Delivery</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full 
                                @if($order->payment_status == 'verified') bg-green-100 text-green-800
                                @elseif($order->payment_status == 'submitted') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        @endif
                    </td>
                    <td class="p-3">
                        <a href="{{ route('seller.orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium inline-flex items-center gap-1">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="p-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                        <p>No orders found for your products</p>
                        <p class="text-sm mt-1">When customers order your products, they will appear here</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($orders->hasPages())
    <div class="p-4 border-t">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<!-- Quick Tips -->
<div class="mt-6 bg-blue-50 rounded-xl p-4 flex gap-3 items-start">
    <i class="fas fa-info-circle text-blue-600 text-xl"></i>
    <div>
        <p class="font-semibold text-blue-800">Order Management Tips</p>
        <p class="text-sm text-blue-700">• Update order status as you process orders to keep customers informed</p>
        <p class="text-sm text-blue-700">• Add tracking numbers once orders are shipped</p>
        <p class="text-sm text-blue-700">• Process return requests within 2-3 business days</p>
    </div>
</div>
@endsection