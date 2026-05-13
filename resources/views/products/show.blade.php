@extends('layouts.master')

@section('title', $product->name)

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="md:flex">
        <div class="md:w-1/2">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
        </div>
        <div class="md:w-1/2 p-6">
            <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
            <p class="text-3xl font-bold text-indigo-600 mt-2">₹{{ number_format($product->price, 2) }}</p>
            
            <div class="mt-4">
                @if($product->quantity == 0)
                    <span class="inline-flex items-center gap-2 bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-times-circle"></i> Out of Stock
                    </span>
                @elseif($product->quantity <= 5)
                    <span class="inline-flex items-center gap-2 bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-exclamation-triangle"></i> Only {{ $product->quantity }} units left!
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-check-circle"></i> In Stock ({{ $product->quantity }} units)
                    </span>
                @endif
            </div>
            
            <p class="text-gray-500 mt-3"><i class="fas fa-store"></i> Seller: {{ $product->seller->name ?? 'Admin' }}</p>
            
            @if(Auth::user()->isUser() && $product->quantity > 0)
                <form action="{{ route('cart.add') }}" method="POST" class="mt-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="flex items-center gap-3">
                        <label class="font-medium">Quantity:</label>
                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->quantity }}" class="w-20 p-2 border rounded-lg text-center">
                        <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </form>
            @elseif(Auth::user()->isUser() && $product->quantity == 0)
                <div class="mt-6 p-4 bg-red-50 text-red-600 rounded-lg text-center">
                    <i class="fas fa-times-circle"></i> This product is currently out of stock
                </div>
            @endif
            
            @if(Auth::user()->id == $product->seller_id || Auth::user()->isAdmin())
                <div class="flex gap-3 mt-6 pt-4 border-t">
                    <a href="{{ route('products.edit', $product) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-edit"></i> Edit Product
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this product?')" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                            <i class="fas fa-trash"></i> Delete Product
                        </button>
                    </form>
                </div>
            @endif
            
            <div class="flex gap-3 mt-4 pt-4 border-t">
                <a href="{{ route('products.index') }}" class="text-indigo-600 hover:underline">← Back to Products</a>
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline">Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection