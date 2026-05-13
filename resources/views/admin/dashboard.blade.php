@extends('layouts.master')

@section('title', 'Admin Dashboard')

@section('content')
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white mb-6">
    <h1 class="text-2xl font-bold">Admin Dashboard 👑</h1>
    <p class="opacity-90 mt-1">Welcome to the admin control panel. You have full control over the system.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Users</p>
                <p class="text-2xl font-bold">{{ \App\Models\User::count() }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-indigo-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm">
            <span class="text-gray-500">Sellers: {{ \App\Models\User::where('role', 'seller')->count() }}</span>
            <span class="text-gray-500 ml-3">Users: {{ \App\Models\User::where('role', 'user')->count() }}</span>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Products</p>
                <p class="text-2xl font-bold">{{ \App\Models\Product::count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-box text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm">
            <span class="text-gray-500">Total Value: ₹{{ number_format(\App\Models\Product::sum(\DB::raw('price * quantity')), 2) }}</span>
        </div>
    </div>
    
    <div class="bg-white rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Orders</p>
                <p class="text-2xl font-bold">{{ \App\Models\Order::count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-3 text-sm">
            <span class="text-gray-500">Revenue: ₹{{ number_format(\App\Models\Order::sum('total_amount'), 2) }}</span>
        </div>
    </div>
</div>
@endsection