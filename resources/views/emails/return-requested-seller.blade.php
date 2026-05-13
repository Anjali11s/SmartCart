@component('mail::message')
# Return Request Submitted 🔄

Hello Seller,

A customer has requested a return for order #{{ $order->id }}.

## Order Details
- **Customer:** {{ $order->user->name }}
- **Order Date:** {{ $order->created_at->format('d M Y, h:i A') }}
- **Order Total:** ₹{{ number_format($order->total_amount, 2) }}

## Return Reason
{{ $order->return_reason }}

## Order Items
@foreach($order->items as $item)
- **{{ $item->product->name }}** x {{ $item->quantity }} = ₹{{ number_format($item->price * $item->quantity, 2) }}
@endforeach

@component('mail::button', ['url' => route('seller.orders.show', $order), 'color' => 'primary'])
View Order & Process Return
@endcomponent

Please review and process this return request within 2-3 business days.

Best regards,<br>
**SmartCart Team**
@endcomponent