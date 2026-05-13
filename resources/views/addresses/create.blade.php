@extends('layouts.master')

@section('title', 'Add Address')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl p-8 shadow-sm">
        <h1 class="text-2xl font-bold mb-6"><i class="fas fa-plus-circle text-indigo-600"></i> Add New Address</h1>
        
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6">
                <strong>Please fix the following errors:</strong>
                <ul class="mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('addresses.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block font-medium mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('full_name') }}" required>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('phone') }}" required>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Alternate Phone</label>
                    <input type="tel" name="alternate_phone" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('alternate_phone') }}">
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Address Type <span class="text-red-500">*</span></label>
                    <select name="address_type" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                        <option value="home" {{ old('address_type') == 'home' ? 'selected' : '' }}>Home</option>
                        <option value="work" {{ old('address_type') == 'work' ? 'selected' : '' }}>Work</option>
                        <option value="other" {{ old('address_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block font-medium mb-1">Address Line 1 <span class="text-red-500">*</span></label>
                <input type="text" name="address_line1" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('address_line1') }}" required>
            </div>
            
            <div class="mb-4">
                <label class="block font-medium mb-1">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('address_line2') }}">
            </div>
            
            <div class="mb-4">
                <label class="block font-medium mb-1">Landmark (Optional)</label>
                <input type="text" name="landmark" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('landmark') }}">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block font-medium mb-1">City <span class="text-red-500">*</span></label>
                    <input type="text" name="city" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('city') }}" required>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">State <span class="text-red-500">*</span></label>
                    <input type="text" name="state" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('state') }}" required>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Pincode <span class="text-red-500">*</span></label>
                    <input type="text" name="pincode" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" value="{{ old('pincode') }}" required>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Country</label>
                    <input type="text" name="country" class="w-full p-2 border rounded-lg bg-gray-100" value="India" readonly>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>
                    <span>Set as default address</span>
                </label>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save"></i> Save Address
                </button>
                <a href="{{ url()->previous() == route('addresses.create') ? route('addresses.index') : url()->previous() }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection