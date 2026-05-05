<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleLoginController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Dashboard with role check 
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if (!$user) {
        return redirect()->route('login');
    }
    
    if ($user->isAdmin()) {
        return view('admin.dashboard');
    } elseif ($user->isSeller()) {
        return view('seller.dashboard');
    } else {
        return view('dashboard');  // User dashboard
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// Email verification notice route 
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Admin only routes 
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->group(function () {
    Route::get('/users', function () {
        return "Admin - User Management";
    })->name('admin.users');
    
    Route::get('/payments', function () {
        return "Admin - Payment Verification";
    })->name('admin.payments');
});

// Seller only routes
Route::middleware(['auth', 'verified', 'seller'])->prefix('seller')->group(function () {
    Route::get('/products', function () {
        return "Seller - Product Management";
    })->name('seller.products');

    Route::get('/dashboard', function () {
        return view('seller.dashboard');
    })->name('seller.dashboard');
    
    Route::get('/orders', function () {
        return "Seller - Orders";
    })->name('seller.orders');
});

// Profile routes 
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('login/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('login.google.callback');

// Auth routes (Breeze default)
require __DIR__.'/auth.php';