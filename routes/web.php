<?php
 
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\CartController; 
use App\Http\Controllers\BudgetController; 
use App\Http\Controllers\ProductController;     
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Budget;
use App\Models\Order;     

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if (!$user) {
        return redirect()->route('login');
    }
    
    if ($user->isAdmin()) {
        return view('admin.dashboard');
    } elseif ($user->isSeller()) {
        return view('seller.dashboard');
    }

    //  USER DASHBOARD WITH REAL DATA 
    
    // 1️. Get user's cart and calculate totals
    $cart = Cart::where('user_id', $user->id)->first();
    $cartItems = [];
    $cartTotal = 0;
    $cartItemCount = 0;
    
    if ($cart) {
        $cartItems = CartItem::where('cart_id', $cart->id)
                             ->with('product')
                             ->get();
        
        foreach ($cartItems as $item) {
            $cartTotal += $item->product->price * $item->quantity;
            $cartItemCount += $item->quantity;
        }
    }
    // 2️.  Get user's budget
    $budget = Budget::where('user_id', $user->id)->first();
    $budgetAmount = $budget ? $budget->amount : 0;
    $budgetRemaining = max(0, $budgetAmount - $cartTotal);
    $budgetPercentage = $budgetAmount > 0 ? round(($cartTotal / $budgetAmount) * 100) : 0;
    
    // 3️. Get user's orders count
    $ordersCount = Order::where('user_id', $user->id)->count();
    
    // 4️. Get recent orders
    $recentOrders = Order::where('user_id', $user->id)
                         ->orderBy('created_at', 'desc')
                         ->limit(5)
                         ->get(); return view('dashboard', compact(
        'cartItems', 
        'cartTotal', 
        'cartItemCount',
        'budget', 
        'budgetAmount', 
        'budgetRemaining', 
        'budgetPercentage',
        'ordersCount',
        'recentOrders'
    ));
    
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

// Google Login Routes  
Route::get('login/google', [GoogleLoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('login.google.callback');

// Auth routes (Breeze default)
require __DIR__.'/auth.php';

// Product management (create, edit, delete) – works for sellers & admins
Route::resource('products', ProductController::class)->middleware(['auth', 'verified']);

// SELLER ROUTES
Route::middleware(['auth', 'seller'])->prefix('seller')->group(function () {
    Route::get('/dashboard', function () { return view('seller.dashboard'); })->name('seller.dashboard');
    Route::get('/orders', function () { return "Seller - Orders"; })->name('seller.orders');
});

// cart routes
Route::middleware(['auth'])->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// budget routes
Route::middleware(['auth'])->prefix('budget')->group(function () {
    Route::get('/', [BudgetController::class, 'index'])->name('budget.index');
    Route::post('/', [BudgetController::class, 'store'])->name('budget.store');
    Route::get('/status', [BudgetController::class, 'getStatus'])->name('budget.status');
    Route::get('/insights', [BudgetController::class, 'insights'])->name('budget.insights');
    Route::get('/can-checkout', [BudgetController::class, 'canCheckout'])->name('budget.can-checkout');
});