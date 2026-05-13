@component('mail::message')
# Refund Processed 💰

Hello **{{ $order->user->name }}**,

Your refund for order #{{ $order->id }} has been processed successfully.

## Refund Details
- **Order Amount:** ₹{{ number_format($order->total_amount, 2) }}
- **Refund Amount:** ₹{{ number_format($order->refund_amount ?? $order->total_amount, 2) }}
- **Refund Date:** {{ now()->format('d M Y, h:i A') }}

The refund amount will be credited to your original payment method within 5-7 business days.

@component('mail::button', ['url' => route('orders.show', $order), 'color' => 'primary'])
View Order
@endcomponent

Thank you for shopping with SmartCart!

Best regards,<br>
**SmartCart Team**
@endcomponent