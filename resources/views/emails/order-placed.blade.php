@component('mail::message')
# Order Confirmed! 🎉

Hello **{{ $order->user->name }}**,

Thank you for shopping with SmartCart! Your order has been placed successfully.

## Order Details
**Order ID:** #{{ $order->id }}
**Order Date:** {{ $order->created_at->format('d M Y, h:i A') }}
**Payment Method:** {{ $order->payment_method == 'COD' ? 'Cash on Delivery' : 'Online Payment' }}
**Total Amount:** ₹{{ number_format($order->total_amount, 2) }}

## Shipping Address
{{ $order->shippingAddress?->full_name ?? 'N/A' }}<br>
{{ $order->shippingAddress?->full_address ?? $order->shipping_address_text }}<br>
Phone: {{ $order->shipping_phone }}

## Order Items
@foreach($order->items as $item)
- **{{ $item->product->name }}** x {{ $item->quantity }} = ₹{{ number_format($item->price * $item->quantity, 2) }}
@endforeach

@component('mail::button', ['url' => route('orders.show', $order)])
Track Your Order
@endcomponent

We'll notify you once your order is shipped.

Thanks for shopping with SmartCart!

Best regards,<br>
**SmartCart Team**
@endcomponent