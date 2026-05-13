@component('mail::message')
# Order Status Updated

Hello **{{ $order->user->name }}**,

Your order #{{ $order->id }} status has been updated to:

## **{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}**

@if($order->order_status == 'shipped' && $order->tracking_number)
**Tracking Number:** {{ $order->tracking_number }}
**Courier:** {{ $order->courier_name ?? 'Not specified' }}
@endif

@if($order->order_status == 'delivered')
We hope you enjoyed your shopping experience! Please take a moment to rate your products.
@endif

@component('mail::button', ['url' => route('orders.show', $order)])
View Order Details
@endcomponent

Thank you for shopping with SmartCart!

Best regards,<br>
**SmartCart Team**
@endcomponent