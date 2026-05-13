@component('mail::message')
# New Order Received! 🛍️

Hello Seller,

A new order has been placed on SmartCart.

## Order Details
**Order ID:** #{{ $order->id }}
**Customer:** {{ $order->user->name }}
**Order Date:** {{ $order->created_at->format('d M Y, h:i A') }}
**Payment Method:** {{ $order->payment_method == 'COD' ? 'Cash on Delivery' : 'Online Payment' }}
**Total Amount:** ₹{{ number_format($order->total_amount, 2) }}

## Shipping Address
{{ $order->shippingAddress?->full_name ?? 'N/A' }}<br>
{{ $order->shippingAddress?->full_address ?? $order->shipping_address_text }}<br>
Phone: {{ $order->shipping_phone }}

## Order Items
@foreach($order->items as $item)
- **{{ $item->product->name }}** (Seller: {{ $item->product->seller->name }}) x {{ $item->quantity }} = ₹{{ number_format($item->price * $item->quantity, 2) }}
@endforeach

@component('mail::button', ['url' => route('seller.orders')])
View Orders
@endcomponent

Please process this order as soon as possible.

Best regards,<br>
**SmartCart Team**
@endcomponent