@extends('layouts.master')

@section('title', 'Pay with Razorpay')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl p-8 shadow-sm text-center">
        <h2 class="text-2xl font-bold mb-4">Complete Payment</h2>
        <p class="text-gray-600 mb-2">Order #{{ $order->id }}</p>
        <p class="text-3xl font-bold text-indigo-600 mb-6">₹{{ number_format($order->total_amount, 2) }}</p>

        <button id="rzp-button" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition">
            <i class="fas fa-credit-card"></i> Pay Now
        </button>

        <p class="text-sm text-gray-500 mt-4">You will be redirected to Razorpay secure checkout.</p>
    </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('rzp-button').onclick = function(e) {
        var options = {
            key: "{{ env('RAZORPAY_KEY') }}",
            amount: "{{ $order->total_amount * 100 }}",
            currency: "INR",
            name: "SmartCart",
            description: "Order #{{ $order->id }}",
            order_id: "{{ $order->razorpay_order_id }}",
            handler: function(response) {
                // Use absolute URL to avoid any routing confusion
                const verifyUrl = "{{ url('/orders/payment/verify') }}";
                fetch(verifyUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: '{{ $order->id }}',
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature
                    })
                })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP ${res.status} - ${res.statusText}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.href = '{{ route("orders.show", $order) }}';
                    } else {
                        alert('Verification failed: ' + (data.message || 'Unknown error'));
                        window.location.href = '{{ route("orders.index") }}';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Network error: ' + error.message + '. Please contact support.');
                    // Even if verification fails, redirect to orders page (you can later update manually)
                    window.location.href = '{{ route("orders.index") }}';
                });
            },
            modal: {
                ondismiss: function() {
                    window.location.href = '{{ route("orders.index") }}';
                }
            }
        };
        var rzp = new Razorpay(options);
        rzp.open();
        e.preventDefault();
    }
</script>
@endsection