@extends('layouts.master')

@section('title', 'Payment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-8 text-center">
        <div class="mb-6">
            <i class="fas fa-qrcode text-6xl text-indigo-600"></i>
        </div>
        <h1 class="text-2xl font-bold mb-2">Complete Your Payment</h1>
        <p class="text-gray-600 mb-6">Order #{{ $order->id }} | Amount: ₹{{ number_format($order->total_amount, 2) }}</p>
        
        <div class="bg-gray-100 p-6 rounded-lg mb-6">
            <p class="font-semibold mb-2">Scan QR Code to Pay</p>
            <div class="bg-white p-4 inline-block rounded-lg">
                <div class="w-48 h-48 bg-gray-200 flex items-center justify-center rounded-lg">
                    <i class="fas fa-qrcode text-6xl text-gray-400"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-3">Use any UPI app to scan and pay</p>
        </div>
        
        <form action="{{ route('orders.payment-proof', $order) }}" method="POST" enctype="multipart/form-data" class="text-left">
            @csrf
            <div class="mb-4">
                <label class="block font-medium mb-1">Transaction ID / UTR Number</label>
                <input type="text" name="transaction_id" class="w-full p-2 border rounded-lg" required>
            </div>
            <div class="mb-6">
                <label class="block font-medium mb-1">Upload Payment Screenshot</label>
                <input type="file" name="payment_screenshot" accept="image/*" class="w-full p-2 border rounded-lg" required>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2 rounded-lg hover:shadow-lg transition">
                Submit Payment Proof
            </button>
        </form>
        
        <div class="mt-6">
            <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:underline">Back to Order</a>
        </div>
    </div>
</div>
@endsection