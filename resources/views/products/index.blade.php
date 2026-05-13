@extends('layouts.master')

@section('title', 'All Products')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">
        <i class="fas fa-boxes"></i> 
        {{ Auth::user()->isSeller() ? 'Manage Products' : 'All Products' }}
    </h1>
    @if(Auth::user()->isSeller())
        <a href="{{ route('products.create') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Add New Product
        </a>
    @endif
</div>

@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
@endif

@if($products->count() > 0)
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
            <div class="p-4">
                <h3 class="font-bold text-lg">{{ $product->name }}</h3>
                <p class="text-indigo-600 font-bold text-xl mt-1">₹{{ number_format($product->price, 2) }}</p>
                
                <div class="mt-2">
                    @if($product->quantity == 0)
                        <span class="text-red-500 text-xs"><i class="fas fa-times-circle"></i> Out of Stock</span>
                    @elseif($product->quantity <= 5)
                        <span class="text-orange-500 text-xs"><i class="fas fa-exclamation-triangle"></i> Only {{ $product->quantity }} left</span>
                    @else
                        <span class="text-green-500 text-xs"><i class="fas fa-check-circle"></i> In Stock</span>
                    @endif
                </div>
                
                <p class="text-gray-400 text-xs mt-2"><i class="fas fa-store"></i> {{ $product->seller->name ?? 'Admin' }}</p>
                
                <div class="flex gap-2 mt-4">
                    <a href="{{ route('products.show', $product) }}" class="flex-1 text-center bg-gray-100 text-gray-700 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                        <i class="fas fa-eye"></i> View
                    </a>
                    @if(Auth::user()->id == $product->seller_id || Auth::user()->isAdmin())
                        <a href="{{ route('products.edit', $product) }}" class="flex-1 text-center bg-yellow-100 text-yellow-700 py-2 rounded-lg text-sm hover:bg-yellow-200 transition">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this product?')" class="w-full bg-red-100 text-red-700 py-2 rounded-lg text-sm hover:bg-red-200 transition">
                                <i class="fas fa-trash"></i> Del
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">
        {{ $products->links() }}
    </div>
@else
    <div class="bg-white rounded-xl p-12 text-center">
        <i class="fas fa-box-open text-5xl text-gray-300"></i>
        <h3 class="text-xl font-semibold text-gray-700 mt-4">No Products Found</h3>
        @if(Auth::user()->isSeller())
            <a href="{{ route('products.create') }}" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg">Add Your First Product</a>
        @endif
    </div>
@endif
@endsection