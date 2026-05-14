@extends('layouts.master')

@section('title', 'Checkout')

@section('content')
<div class="mb-4">
    <a href="{{ route('cart.index') }}" class="text-indigo-600 hover:text-indigo-800">
        <i class="fas fa-arrow-left"></i> Back to Cart
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Left Column -->
    <div>
        <!-- Address Section -->
        <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
            <h2 class="text-lg font-bold mb-4"><i class="fas fa-map-marker-alt text-indigo-600"></i> Shipping Address</h2>
            
            @if($addresses->count() > 0)
                @foreach($addresses as $address)
                <div class="address-card border-2 rounded-lg p-4 mb-3 cursor-pointer transition" 
                     onclick="selectAddress({{ $address->id }})" 
                     data-address-id="{{ $address->id }}"
                     style="{{ ($defaultAddress && $defaultAddress->id == $address->id) ? 'border-color: #667eea; background: #f0f4ff;' : 'border-color: #e5e7eb;' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <strong>{{ $address->full_name }}</strong>
                            @if($address->is_default)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full ml-2">Default</span>
                            @endif
                        </div>
                        <span class="text-sm text-gray-500">{{ ucfirst($address->address_type) }}</span>
                    </div>
                    <div class="text-gray-600 text-sm mt-2">{{ $address->full_address }}</div>
                    <div class="text-gray-500 text-sm mt-1">Phone: {{ $address->phone }}</div>
                </div>
                @endforeach
                
                <div class="mt-3 text-center">
                    <a href="{{ route('addresses.create') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                        <i class="fas fa-plus"></i> Add New Address
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-map-marker-alt text-4xl text-gray-300"></i>
                    <p class="text-gray-500 mt-2">No addresses saved</p>
                    <a href="{{ route('addresses.create') }}" class="inline-block mt-3 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">
                        Add New Address
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Payment Section -->
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <h2 class="text-lg font-bold mb-4"><i class="fas fa-credit-card text-indigo-600"></i> Payment Method</h2>
            
            <div class="payment-method border-2 rounded-lg p-4 mb-3 cursor-pointer transition" 
                 onclick="selectPayment('COD')" data-payment="COD">
                <div class="flex items-center gap-3">
                    <input type="radio" name="payment_method" value="COD" id="cod" class="w-4 h-4">
                    <label for="cod" class="flex-1 cursor-pointer">
                        <strong>Cash on Delivery</strong>
                        <div class="text-sm text-gray-500">Pay when you receive the product</div>
                    </label>
                    <i class="fas fa-money-bill-wave text-2xl text-indigo-600"></i>
                </div>
            </div>
            
            <div class="payment-method border-2 rounded-lg p-4 mb-3 cursor-pointer transition" 
                 onclick="selectPayment('QR')" data-payment="QR">
                <div class="flex items-center gap-3">
                    <input type="radio" name="payment_method" value="QR" id="qr" class="w-4 h-4">
                    <label for="qr" class="flex-1 cursor-pointer">
                        <strong>QR Code Payment</strong>
                        <div class="text-sm text-gray-500">Pay via UPI, Card, or Netbanking</div>
                    </label>
                    <i class="fas fa-qrcode text-2xl text-indigo-600"></i>
                </div>
            </div>
            
            <!-- Razorpay Option - NEW -->
            <div class="payment-method border-2 rounded-lg p-4 mb-3 cursor-pointer transition" 
                 onclick="selectPayment('razorpay')" data-payment="razorpay">
                <div class="flex items-center gap-3">
                    <input type="radio" name="payment_method" value="razorpay" id="razorpay" class="w-4 h-4">
                    <label for="razorpay" class="flex-1 cursor-pointer">
                        <strong>Razorpay (Card/UPI/Netbanking)</strong>
                        <div class="text-sm text-gray-500">Instant online payment</div>
                    </label>
                    <i class="fas fa-credit-card text-2xl text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Order Summary -->
    <div>
        <div class="bg-white rounded-xl p-6 shadow-sm sticky top-4">
            <h2 class="text-lg font-bold mb-4"><i class="fas fa-shopping-bag text-indigo-600"></i> Order Summary</h2>
            
            <div class="max-h-96 overflow-y-auto">
                @foreach($cartItems as $item)
                <div class="flex justify-between py-3 border-b">
                    <div>
                        <strong>{{ $item->product->name }}</strong>
                        <div class="text-sm text-gray-500">Qty: {{ $item->quantity }}</div>
                    </div>
                    <div class="font-semibold">₹{{ number_format($item->product->price * $item->quantity, 2) }}</div>
                </div>
                @endforeach
            </div>
            
            <div class="flex justify-between py-3 border-t mt-3 font-bold text-lg">
                <span>Total</span>
                <span class="text-indigo-600">₹{{ number_format($total, 2) }}</span>
            </div>
            
            <form id="orderForm" action="{{ route('orders.place') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="address_id" id="selectedAddress" value="{{ $defaultAddress?->id }}">
                <input type="hidden" name="payment_method" id="selectedPayment" value="">
                
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:shadow-lg transition">
                    <i class="fas fa-check-circle"></i> Place Order
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedAddressId = {{ $defaultAddress?->id ?? 'null' }};
    let selectedPayment = '';
    
    function selectAddress(id) {
        selectedAddressId = id;
        document.getElementById('selectedAddress').value = id;
        document.querySelectorAll('.address-card').forEach(card => {
            card.style.borderColor = '#e5e7eb';
            card.style.background = 'white';
        });
        document.querySelector(`.address-card[data-address-id="${id}"]`).style.borderColor = '#667eea';
        document.querySelector(`.address-card[data-address-id="${id}"]`).style.background = '#f0f4ff';
    }
    
    function selectPayment(method) {
        selectedPayment = method;
        document.getElementById('selectedPayment').value = method;
        document.querySelectorAll('.payment-method').forEach(m => {
            m.style.borderColor = '#e5e7eb';
            m.style.background = 'white';
            m.querySelector('input').checked = false;
        });
        document.querySelector(`.payment-method[data-payment="${method}"]`).style.borderColor = '#667eea';
        document.querySelector(`.payment-method[data-payment="${method}"]`).style.background = '#f0f4ff';
        document.querySelector(`input[value="${method}"]`).checked = true;
    }
    
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        if (!selectedAddressId) {
            e.preventDefault();
            alert('Please select a shipping address');
            return false;
        }
        if (!selectedPayment) {
            e.preventDefault();
            alert('Please select a payment method');
            return false;
        }
    });
</script>
@endpush
@endsection