@component('mail::message')
# Budget Alert!

Hello {{ $user->name }},

Your current cart total (**₹{{ number_format($cartTotal, 2) }}**) has exceeded your monthly budget of **₹{{ number_format($budgetAmount, 2) }}**.

**Exceeded by:** ₹{{ number_format($cartTotal - $budgetAmount, 2) }}

## Recommendations:
1. Review your cart and remove unnecessary items
2. Consider increasing your budget if needed
3. Look for cheaper alternatives

@component('mail::button', ['url' => route('cart.index')])
View Your Cart
@endcomponent

@component('mail::button', ['url' => route('budget.index'), 'color' => 'green'])
Adjust Budget
@endcomponent

Thanks for shopping smartly with SmartCart!

Best regards,<br>
**SmartCart Team**
@endcomponent