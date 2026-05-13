@component('mail::message')
# Return Request Approved

Hello **{{ $order->user->name }}**,

Your return request for order #{{ $order->id }} has been **approved**.

## Refund Details
- **Order Amount:** ₹{{ number_format($order->total_amount, 2) }}
- **Refund Status:** Processing
- **Expected Timeline:** 5-7 business days

The refund will be credited to your original payment method.

@component('mail::button', ['url' => route('orders.show', $order)])
View Order
@endcomponent

Thank you for shopping with SmartCart!

Best regards,<br>
**SmartCart Team**
@endcomponent