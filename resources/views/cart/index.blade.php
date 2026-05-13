@extends('layouts.master')

@section('title', 'My Cart')

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-4 border-b bg-gray-50">
        <h1 class="text-xl font-bold"><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
    </div>
    
    @if($cartItems->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-left">Price</th>
                        <th class="p-3 text-center">Quantity</th>
                        <th class="p-3 text-right">Subtotal</th>
                        <th class="p-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                    <tr class="border-b">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                                <div>
                                    <p class="font-medium">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">Seller: {{ $item->product->seller->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-3">₹{{ number_format($item->product->price, 2) }}</td>
                        <td class="p-3">
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="update-form flex items-center justify-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item->quantity }}" 
                                       min="1" max="{{ $item->product->quantity }}" 
                                       class="w-16 p-1 border rounded text-center">
                                <button type="submit" class="text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </td>
                        <td class="p-3 text-right font-semibold item-subtotal">
                            ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                        </td>
                        <td class="p-3 text-center">
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Remove this item?')" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 bg-gray-50 border-t flex justify-between items-center flex-wrap gap-3">
            <div>
                <span class="text-gray-600">Total Amount:</span>
                <span class="text-2xl font-bold text-indigo-600 cart-total">₹{{ number_format($total, 2) }}</span>
            </div>
            <div class="flex gap-3">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Clear entire cart?')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-trash-alt"></i> Clear Cart
                    </button>
                </form>
                <a href="{{ route('orders.checkout') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                </a>
            </div>
        </div>
    @else
        <div class="p-12 text-center">
            <i class="fas fa-shopping-cart text-5xl text-gray-300"></i>
            <h3 class="text-xl font-semibold text-gray-700 mt-4">Your cart is empty!</h3>
            <p class="text-gray-500 mt-2">Looks like you haven't added any items yet.</p>
            <a href="{{ route('products.index') }}" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-store"></i> Continue Shopping
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.querySelectorAll('.update-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const url = form.action;
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    const row = form.closest('tr');
                    row.querySelector('.item-subtotal').textContent = '₹' + data.item_total.toFixed(2);
                    document.querySelector('.cart-total').textContent = '₹' + data.cart_total.toFixed(2);
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    });
</script>
@endpush
@endsection