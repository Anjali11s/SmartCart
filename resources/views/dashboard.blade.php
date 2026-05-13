@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white mb-6">
    <h1 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h1>
    <p class="opacity-90 mt-1">Track your spending, manage your cart, and shop smarter with SmartCart.</p>
</div>

@php
    $cart = Auth::user()->cart;
    $cartItemCount = $cart?->items->sum('quantity') ?? 0;
    $cartTotal = $cart?->items->sum(function($item) {
        return $item->product->price * $item->quantity;
    }) ?? 0;
    $budget = Auth::user()->budget;
    $budgetAmount = $budget?->amount ?? 0;
    
    // ✅ FIXED: Calculate monthly spent
    $monthlySpent = Auth::user()->orders()
        ->where('order_status', '!=', 'cancelled')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');
    
    $totalCommitted = $monthlySpent + $cartTotal;
    $budgetRemaining = $budget ? max(0, $budgetAmount - $totalCommitted) : 0;
    $budgetPercentage = $budget && $budgetAmount > 0 ? min(100, round(($totalCommitted / $budgetAmount) * 100)) : 0;
    $ordersCount = Auth::user()->orders()->count();
    $recentOrders = Auth::user()->orders()->latest()->take(5)->get();
@endphp

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Cart Items</p>
                <p class="text-2xl font-bold">{{ $cartItemCount }}</p>
                @if($cartTotal > 0)
                    <p class="text-xs text-gray-400 mt-1">Total: ₹{{ number_format($cartTotal, 2) }}</p>
                @endif
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-cart text-indigo-600 text-xl"></i>
            </div>
        </div>
        <a href="{{ route('cart.index') }}" class="text-indigo-600 text-sm mt-3 inline-block">View Cart →</a>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Budget Status</p>
                @if($budget)
                    <p class="text-2xl font-bold {{ $budgetPercentage >= 100 ? 'text-red-600' : ($budgetPercentage >= 70 ? 'text-orange-600' : 'text-green-600') }}">
                        {{ $budgetPercentage }}%
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        Used: ₹{{ number_format($totalCommitted) }} / ₹{{ number_format($budgetAmount) }}
                    </p>
                    @if($monthlySpent > 0)
                        <p class="text-xs text-gray-400">Spent this month: ₹{{ number_format($monthlySpent) }}</p>
                    @endif
                @else
                    <p class="text-xl font-bold text-gray-400">Not Set</p>
                @endif
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-green-600 text-xl"></i>
            </div>
        </div>
        <a href="{{ route('budget.index') }}" class="text-indigo-600 text-sm mt-3 inline-block">Manage Budget →</a>
    </div>
    
    <div class="bg-white rounded-xl p-5 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Orders Placed</p>
                <p class="text-2xl font-bold">{{ $ordersCount }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-blue-600 text-xl"></i>
            </div>
        </div>
        <a href="{{ route('orders.index') }}" class="text-indigo-600 text-sm mt-3 inline-block">View Orders →</a>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-3 mb-6">
    <a href="{{ route('products.index') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2 rounded-lg hover:shadow-lg transition flex items-center gap-2">
        <i class="fas fa-store"></i> Browse Products
    </a>
    <a href="{{ route('cart.index') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
        <i class="fas fa-shopping-cart"></i> My Cart
        @if($cartItemCount > 0)
            <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5 ml-1">{{ $cartItemCount }}</span>
        @endif
    </a>
    <a href="{{ route('budget.index') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
        <i class="fas fa-wallet"></i> Set Budget
    </a>
    <a href="{{ route('addresses.index') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition flex items-center gap-2">
        <i class="fas fa-map-marker-alt"></i> My Addresses
    </a>
</div>

<!-- Recent Orders Section -->
<div class="bg-white rounded-xl shadow-sm">
    <div class="p-4 border-b">
        <h3 class="font-bold"><i class="fas fa-history text-indigo-600"></i> Recent Orders</h3>
    </div>
    
    @if($recentOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left text-sm font-medium text-gray-500">Order ID</th>
                        <th class="p-3 text-left text-sm font-medium text-gray-500">Date</th>
                        <th class="p-3 text-left text-sm font-medium text-gray-500">Total</th>
                        <th class="p-3 text-left text-sm font-medium text-gray-500">Status</th>
                        <th class="p-3 text-left text-sm font-medium text-gray-500">Payment</th>
                        <th class="p-3 text-left text-sm font-medium text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-medium">#{{ $order->id }}</td>
                        <td class="p-3 text-sm">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="p-3 font-semibold">₹{{ number_format($order->total_amount, 2) }}</td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($order->order_status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->order_status == 'processing') bg-blue-100 text-blue-800
                                @elseif($order->order_status == 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->order_status == 'delivered') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                            </span>
                        </td>
                        <td class="p-3">
                            <span class="text-xs {{ $order->payment_status == 'verified' ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="p-3">
                            <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 text-sm hover:underline">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t text-center">
            <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:underline text-sm">View All Orders →</a>
        </div>
    @else
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-shopping-bag text-4xl mb-3 opacity-50"></i>
            <p>No orders yet</p>
            <a href="{{ route('products.index') }}" class="text-indigo-600 text-sm mt-2 inline-block">Start Shopping →</a>
        </div>
    @endif
</div>
@endsection