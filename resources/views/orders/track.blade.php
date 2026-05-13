@extends('layouts.master')

@section('title', 'Track Order #' . $order->id)

@section('content')
<div class="mb-4">
    <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left"></i> Back to Order Details
    </a>
</div>

<div class="bg-white rounded-xl p-6 shadow-sm">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold">Track Order #{{ $order->id }}</h1>
        <p class="text-gray-500 mt-1">Current Status: 
            <span class="px-3 py-1 rounded-full text-sm {{ $order->status_badge_class }}">
                {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
            </span>
        </p>
    </div>
    
    <div class="max-w-2xl mx-auto">
        @php
            $steps = [
                ['name' => 'Order Placed', 'icon' => 'fa-shopping-cart', 'time' => $order->created_at],
                ['name' => 'Order Confirmed', 'icon' => 'fa-check-circle', 'time' => $order->order_status != 'pending' ? $order->created_at->addHours(2) : null],
                ['name' => 'Processing', 'icon' => 'fa-cogs', 'time' => in_array($order->order_status, ['processing', 'shipped', 'delivered']) ? $order->created_at->addHours(24) : null],
                ['name' => 'Shipped', 'icon' => 'fa-shipping-fast', 'time' => in_array($order->order_status, ['shipped', 'delivered']) ? $order->updated_at : null],
                ['name' => 'Out for Delivery', 'icon' => 'fa-truck', 'time' => $order->order_status == 'delivered' ? $order->delivered_at?->subHours(5) : null],
                ['name' => 'Delivered', 'icon' => 'fa-check-double', 'time' => $order->order_status == 'delivered' ? $order->delivered_at : null],
            ];
        @endphp
        
        @foreach($steps as $step)
            @php
                $isCompleted = !is_null($step['time']);
                $isActive = ($order->order_status == 'processing' && $step['name'] == 'Processing') ||
                           ($order->order_status == 'shipped' && $step['name'] == 'Shipped') ||
                           ($order->order_status == 'out_for_delivery' && $step['name'] == 'Out for Delivery');
            @endphp
            <div class="relative pl-8 pb-6 border-l-3 {{ $isCompleted ? 'border-green-500' : 'border-gray-300' }}">
                <div class="absolute left-[-0.65rem] top-0 w-5 h-5 rounded-full {{ $isCompleted ? 'bg-green-500' : ($isActive ? 'bg-indigo-500 animate-pulse' : 'bg-gray-300') }}"></div>
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $isCompleted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                        <i class="fas {{ $step['icon'] }} text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold {{ $isCompleted ? 'text-gray-800' : 'text-gray-400' }}">{{ $step['name'] }}</h3>
                        @if($step['time'])
                            <p class="text-xs text-gray-500">{{ $step['time']->format('d M Y, h:i A') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    @if($order->tracking_number)
    <div class="mt-6 p-4 bg-gray-50 rounded-lg text-center">
        <p class="text-sm text-gray-600">Tracking Number: <strong class="font-mono">{{ $order->tracking_number }}</strong></p>
        <p class="text-sm text-gray-600">Courier: <strong>{{ $order->courier_name ?? 'Not assigned yet' }}</strong></p>
    </div>
    @endif
</div>
@endsection