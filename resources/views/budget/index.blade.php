@extends('layouts.master')

@section('title', 'Budget Tracker')

@section('content')
@php
    $user = Auth::user();
    $budget = $user->budget;
    
    // Only count DELIVERED orders for monthly spent
    $monthlySpent = $user->orders()
        ->where('order_status', 'delivered')
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');
    
    $cartTotal = $user->cart?->items->sum(fn($i) => $i->product->price * $i->quantity) ?? 0;
    $totalCommitted = $monthlySpent + $cartTotal;
    $remainingBudget = $budget ? max(0, $budget->amount - $totalCommitted) : 0;
    $percentageUsed = $budget && $budget->amount > 0 ? min(100, round(($totalCommitted / $budget->amount) * 100)) : 0;
@endphp

<div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white mb-6">
    <h1 class="text-2xl font-bold"><i class="fas fa-chart-line"></i> Your Budget Overview</h1>
    @if($budget)
        <p class="text-4xl font-bold mt-2">₹{{ number_format($budget->amount, 2) }}</p>
        <p class="opacity-90">Monthly spending limit</p>
    @else
        <p class="text-3xl font-bold mt-2">Not Set</p>
        <p class="opacity-90">Set your monthly budget to start tracking</p>
    @endif
</div>

<div class="bg-white rounded-xl p-6 shadow-sm mb-6">
    <h3 class="text-lg font-bold mb-4"><i class="fas fa-chart-simple"></i> Real-time Budget Status</h3>
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <p class="text-gray-500 text-sm">Monthly Spent</p>
            <p class="text-2xl font-bold text-orange-600">₹{{ number_format($monthlySpent, 2) }}</p>
            <p class="text-xs text-gray-400">From delivered orders</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <p class="text-gray-500 text-sm">Cart Total</p>
            <p class="text-2xl font-bold text-blue-600" id="cartTotal">₹{{ number_format($cartTotal, 2) }}</p>
            <p class="text-xs text-gray-400">Pending in cart</p>
        </div>
        <div class="bg-gray-50 rounded-xl p-4 text-center">
            <p class="text-gray-500 text-sm">Remaining</p>
            <p class="text-2xl font-bold text-green-600" id="remainingAmount">
                ₹{{ number_format($remainingBudget, 2) }}
            </p>
            <p class="text-xs text-gray-400">Available to spend</p>
        </div>
    </div>
    
    <div class="mb-4">
        <div class="flex justify-between text-sm mb-1">
            <span>Budget Usage</span>
            <span class="{{ $percentageUsed >= 100 ? 'text-red-600' : ($percentageUsed >= 70 ? 'text-orange-600' : 'text-green-600') }}">
                {{ $percentageUsed }}%
            </span>
        </div>
        <div class="bg-gray-200 rounded-full h-4 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500" 
                 style="width: {{ $percentageUsed }}%; background: linear-gradient(90deg, #10b981, #667eea, #ef4444);"></div>
        </div>
    </div>
    
    <div class="text-sm text-gray-600 mb-4">
        <p><strong>Total Committed:</strong> ₹{{ number_format($totalCommitted) }} / ₹{{ number_format($budget?->amount ?? 0) }}</p>
        @if($monthlySpent > 0)
            <p class="text-green-600 mt-1">✅ Already spent: ₹{{ number_format($monthlySpent) }} on delivered orders</p>
        @endif
        @if($cartTotal > 0)
            <p class="text-orange-500 mt-1">⏳ Pending in cart: ₹{{ number_format($cartTotal) }}</p>
        @endif
    </div>
    
    @if($budget && $totalCommitted > $budget->amount)
        <div class="p-4 rounded-lg bg-red-100 text-red-700 mb-4">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Budget Exceeded!</strong> You have exceeded your budget by ₹{{ number_format($totalCommitted - $budget->amount) }}
        </div>
    @elseif($budget && $percentageUsed >= 70)
        <div class="p-4 rounded-lg bg-yellow-100 text-yellow-700 mb-4">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Budget Warning!</strong> You have used {{ $percentageUsed }}% of your budget.
        </div>
    @elseif(!$budget)
        <div class="p-4 rounded-lg bg-blue-100 text-blue-700 mb-4">
            <i class="fas fa-info-circle"></i>
            No budget set. Click below to set your monthly budget.
        </div>
    @endif
    
    <div class="flex gap-3">
        <a href="{{ route('cart.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-shopping-cart"></i> View Cart
        </a>
        <a href="{{ route('budget.insights') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
            <i class="fas fa-chart-line"></i> View Insights
        </a>
    </div>
</div>

<div class="bg-white rounded-xl p-6 shadow-sm">
    <h3 class="text-lg font-bold mb-4">
        <i class="fas fa-pen"></i> {{ $budget ? 'Update Your Budget' : 'Set Your Monthly Budget' }}
    </h3>
    
    <form method="POST" action="{{ route('budget.store') }}" id="budgetForm">
        @csrf
        <div class="mb-4">
            <label class="block font-medium mb-2">Monthly Budget Amount (₹)</label>
            <input type="number" name="amount" value="{{ $budget ? $budget->amount : '' }}" 
                   class="w-full p-3 border rounded-lg text-lg" placeholder="e.g., 5000" step="0.01" min="0" required>
            <p class="text-gray-500 text-sm mt-2">Set a realistic budget to help track your spending habits.</p>
        </div>
        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition">
            <i class="fas fa-save"></i> {{ $budget ? 'Update Budget' : 'Set Budget' }}
        </button>
    </form>
</div>

@push('scripts')
<script>
    function updateBudgetStatus() {
        fetch('{{ route("budget.status") }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cartTotal').innerHTML = '₹' + data.cart_total;
                    document.getElementById('remainingAmount').innerHTML = '₹' + data.remaining;
                }
            })
            .catch(error => console.error('Error:', error));
    }
    setInterval(updateBudgetStatus, 30000);
</script>
@endpush
@endsection