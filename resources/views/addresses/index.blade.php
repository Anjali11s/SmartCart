@extends('layouts.master')

@section('title', 'My Addresses')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold"><i class="fas fa-map-marker-alt"></i> My Addresses</h1>
    <a href="{{ route('addresses.create') }}" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:shadow-lg transition">
        <i class="fas fa-plus"></i> Add New Address
    </a>
</div>

@if($addresses->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($addresses as $address)
        <div class="bg-white rounded-xl p-5 shadow-sm border-2 {{ $address->is_default ? 'border-green-500' : 'border-transparent' }}">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="font-bold text-lg">{{ $address->full_name }}</h3>
                    @if($address->is_default)
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full mt-1 inline-block">Default Address</span>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('addresses.edit', $address) }}" class="text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline" onsubmit="return confirm('Delete this address?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-gray-600 space-y-1">
                <p><i class="fas fa-phone-alt w-5"></i> {{ $address->phone }}</p>
                <p><i class="fas fa-location-dot w-5"></i> {{ $address->full_address }}</p>
                <p><i class="fas fa-tag w-5"></i> {{ ucfirst($address->address_type) }}</p>
            </div>
            @if(!$address->is_default)
            <div class="mt-3">
                <form action="{{ route('addresses.set-default', $address) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-check-circle"></i> Set as Default
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endforeach
    </div>
@else
    <div class="bg-white rounded-xl p-12 text-center">
        <i class="fas fa-map-marker-alt text-5xl text-gray-300"></i>
        <h3 class="text-xl font-semibold text-gray-700 mt-4">No Addresses Saved</h3>
        <p class="text-gray-500 mt-2">Add your first address to start shopping!</p>
        <a href="{{ route('addresses.create') }}" class="inline-block mt-4 bg-indigo-600 text-white px-6 py-2 rounded-lg">Add New Address</a>
    </div>
@endif
@endsection