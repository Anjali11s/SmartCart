@extends('layouts.master')

@section('title', 'Seller Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Seller Dashboard 👑</h1>
    <p class="text-gray-500">Manage your products, track orders, and grow your business</p>
</div>

@php
    $seller = Auth::user();
    
    // PRODUCT STATS (Only existing products)
    $totalProducts = $seller->products()->count();
    $lowStockProducts = $seller->products()->where('quantity', '<=', 5)->where('quantity', '>', 0)->count();
    $outOfStockProducts = $seller->products()->where('quantity', 0)->count();
    $totalValue = $seller->products()->sum(\DB::raw('price * quantity'));
    
    // ORDER STATS (Counts all orders that contain seller's products - including deleted ones)
    // Using withTrashed() to include orders from deleted products
    $totalOrders = \App\Models\Order::whereHas('items', function($q) use ($seller) {
        $q->whereHas('product', function($subQ) use ($seller) {
            $subQ->where('seller_id', $seller->id)->withTrashed();
        });
    })->count();
    
    $pendingOrders = \App\Models\Order::whereHas('items', function($q) use ($seller) {
        $q->whereHas('product', function($subQ) use ($seller) {
            $subQ->where('seller_id', $seller->id)->withTrashed();
        });
    })->where('order_status', 'pending')->count();
    
    $processingOrders = \App\Models\Order::whereHas('items', function($q) use ($seller) {
        $q->whereHas('product', function($subQ) use ($seller) {
            $subQ->where('seller_id', $seller->id)->withTrashed();
        });
    })->where('order_status', 'processing')->count();
    
    $shippedOrders = \App\Models\Order::whereHas('items', function($q) use ($seller) {
        $q->whereHas('product', function($subQ) use ($seller) {
            $subQ->where('seller_id', $seller->id)->withTrashed();
        });
    })->where('order_status', 'shipped')->count();
    
    $deliveredOrders = \App\Models\Order::whereHas('items', function($q) use ($seller) {
        $q->whereHas('product', function($subQ) use ($seller) {
            $subQ->where('seller_id', $seller->id)->withTrashed();
        });
    })->where('order_status', 'delivered')->count();
    
    // EARNINGS STATS (Using order_items.price - preserves history even if product deleted)
    $totalEarnings = \App\Models\OrderItem::whereHas('product', function($q) use ($seller) {
        $q->where('seller_id', $seller->id)->withTrashed();
    })->whereHas('order', function($q) {
        $q->where('order_status', 'delivered');
    })->sum(\DB::raw('price * quantity * 0.90'));
    
    $recentProducts = $seller->products()->latest()->take(5)->get();
    
    // RECENT ORDERS (For display in dashboard)
    $recentOrders = \App\Models\Order::whereHas('items', function($q) use ($seller) {
        $q->whereHas('product', function($subQ) use ($seller) {
            $subQ->where('seller_id', $seller->id)->withTrashed();
        });
    })->with('user')->latest()->take(5)->get();
@endphp

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Products</p>
                <p class="text-2xl font-bold">{{ $totalProducts }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-box text-indigo-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Low Stock</p>
                <p class="text-2xl font-bold text-orange-600">{{ $lowStockProducts }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Orders</p>
                <p class="text-2xl font-bold">{{ $totalOrders }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-1">Includes deleted products</p>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Earnings</p>
                <p class="text-2xl font-bold text-green-600">₹{{ number_format($totalEarnings, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-rupee-sign text-green-600 text-xl"></i>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-1">From delivered orders only</p>
    </div>
</div>

<!-- Order Status Cards -->
<div class="grid grid-cols-1 sm:grid-cols-5 gap-4 mb-6">
    <div class="bg-yellow-50 rounded-xl p-4 border-l-4 border-yellow-500">
        <p class="text-yellow-600 text-sm">Pending Orders</p>
        <p class="text-2xl font-bold text-yellow-700">{{ $pendingOrders }}</p>
        <a href="{{ route('seller.orders', ['status' => 'pending']) }}" class="text-xs text-yellow-600 mt-1 inline-block">View →</a>
    </div>
    <div class="bg-blue-50 rounded-xl p-4 border-l-4 border-blue-500">
        <p class="text-blue-600 text-sm">Processing</p>
        <p class="text-2xl font-bold text-blue-700">{{ $processingOrders }}</p>
        <a href="{{ route('seller.orders', ['status' => 'processing']) }}" class="text-xs text-blue-600 mt-1 inline-block">View →</a>
    </div>
    <div class="bg-purple-50 rounded-xl p-4 border-l-4 border-purple-500">
        <p class="text-purple-600 text-sm">Shipped</p>
        <p class="text-2xl font-bold text-purple-700">{{ $shippedOrders }}</p>
        <a href="{{ route('seller.orders', ['status' => 'shipped']) }}" class="text-xs text-purple-600 mt-1 inline-block">View →</a>
    </div>
    <div class="bg-green-50 rounded-xl p-4 border-l-4 border-green-500">
        <p class="text-green-600 text-sm">Delivered</p>
        <p class="text-2xl font-bold text-green-700">{{ $deliveredOrders }}</p>
        <a href="{{ route('seller.orders', ['status' => 'delivered']) }}" class="text-xs text-green-600 mt-1 inline-block">View →</a>
    </div>
    <div class="bg-red-50 rounded-xl p-4 border-l-4 border-red-500">
        <p class="text-red-600 text-sm">Out of Stock</p>
        <p class="text-2xl font-bold text-red-700">{{ $outOfStockProducts }}</p>
        <a href="{{ route('products.index') }}" class="text-xs text-red-600 mt-1 inline-block">Restock →</a>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-3 mb-6">
    <a href="{{ route('products.create') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2 rounded-lg hover:shadow-lg transition flex items-center gap-2">
        <i class="fas fa-plus-circle"></i> Add New Product
    </a>
    <a href="{{ route('products.index') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
        <i class="fas fa-boxes"></i> Manage Products
    </a>
    <a href="{{ route('seller.orders') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
        <i class="fas fa-truck"></i> View All Orders
    </a>
</div>

<!-- Recent Products & Orders -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Products -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-4 border-b">
            <h3 class="font-bold"><i class="fas fa-clock text-indigo-600"></i> Recently Added Products</h3>
        </div>
        <div class="divide-y">
            @forelse($recentProducts as $product)
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ $product->image_url }}" class="w-12 h-12 object-cover rounded-lg" alt="{{ $product->name }}">
                    <div>
                        <p class="font-medium">{{ $product->name }}</p>
                        <p class="text-sm text-gray-500">₹{{ number_format($product->price, 2) }} | Stock: {{ $product->quantity }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('products.edit', $product) }}" class="text-orange-500 hover:text-orange-700">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-box-open text-3xl mb-2"></i>
                <p>No products yet</p>
                <a href="{{ route('products.create') }}" class="text-indigo-600 text-sm mt-2 inline-block">Add your first product →</a>
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="p-4 border-b">
            <h3 class="font-bold"><i class="fas fa-shopping-cart text-indigo-600"></i> Recent Orders</h3>
        </div>
        <div class="divide-y">
            @forelse($recentOrders as $order)
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="font-medium">Order #{{ $order->id }}</p>
                        <p class="text-sm text-gray-500">{{ $order->user->name }} | {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        @if($order->order_status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->order_status == 'processing') bg-blue-100 text-blue-800
                        @elseif($order->order_status == 'shipped') bg-purple-100 text-purple-800
                        @elseif($order->order_status == 'delivered') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-sm font-semibold">₹{{ number_format($order->total_amount, 2) }}</p>
                    <a href="{{ route('seller.orders.show', $order) }}" class="text-indigo-600 text-sm hover:underline">View Details →</a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-inbox text-3xl mb-2"></i>
                <p>No orders yet</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Tips -->
<div class="mt-6 bg-amber-50 rounded-xl p-4 flex gap-3 items-start">
    <i class="fas fa-lightbulb text-amber-600 text-xl"></i>
    <div>
        <p class="font-semibold text-amber-800">Seller Tips</p>
        <p class="text-sm text-amber-700">Keep your products in stock to maximize sales. Add high-quality images to attract more buyers. Process orders quickly for better ratings.</p>
        <p class="text-xs text-amber-600 mt-1">Note: Orders from deleted products still appear in your history with "Product Unavailable" label.</p>
    </div>
</div>
@endsection