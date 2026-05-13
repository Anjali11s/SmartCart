@component('mail::message')
# Refund Processed

Hello Seller,

Refund has been processed for order #{{ $order->id }}.

## Refund Details
- **Order Amount:** ₹{{ number_format($order->total_amount, 2) }}
- **Refund Amount:** ₹{{ number_format($order->refund_amount ?? $order->total_amount, 2) }}
- **Your Earnings Deducted:** ₹{{ number_format($order->seller_earnings, 2) }}

The refund amount has been deducted from your earnings.

@component('mail::button', ['url' => route('seller.orders.show', $order), 'color' => 'primary'])
View Order
@endcomponent

Best regards,<br>
**SmartCart Team**
@endcomponent