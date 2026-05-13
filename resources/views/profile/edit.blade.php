@extends('layouts.master')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <h1 class="text-xl font-bold text-white"><i class="fas fa-user"></i> Profile Settings</h1>
        </div>
        
        <div class="p-6">
            @if(session('status') == 'profile-updated')
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6">
                    <i class="fas fa-check-circle"></i> Profile updated successfully!
                </div>
            @endif
            
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Name</label>
                    <input type="text" name="name" class="w-full p-2 border rounded-lg" value="{{ old('name', $user->name) }}" required>
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Email</label>
                    <input type="email" name="email" class="w-full p-2 border rounded-lg" value="{{ old('email', $user->email) }}" required>
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:shadow-lg transition">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-key"></i> Change Password</h2>
        </div>
        
        <div class="p-6">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Current Password</label>
                    <input type="password" name="current_password" class="w-full p-2 border rounded-lg" required>
                    @error('current_password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">New Password</label>
                    <input type="password" name="password" class="w-full p-2 border rounded-lg" required>
                </div>
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="w-full p-2 border rounded-lg" required>
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <button type="submit" class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition">
                    <i class="fas fa-key"></i> Update Password
                </button>
            </form>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="bg-gradient-to-r from-red-500 to-pink-500 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-trash"></i> Delete Account</h2>
        </div>
        
        <div class="p-6">
            <p class="text-gray-600 mb-4">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
            
            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account?')">
                @csrf
                @method('DELETE')
                
                <div class="mb-4">
                    <label class="block font-medium mb-1">Password</label>
                    <input type="password" name="password" class="w-full p-2 border rounded-lg" placeholder="Enter your password to confirm" required>
                    @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                
                <button type="submit" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition">
                    <i class="fas fa-trash"></i> Delete Account
                </button>
            </form>
        </div>
    </div>
</div>
@endsection