<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSeller()) {
            return redirect()->route('seller.dashboard');
        }
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        // User Dashboard Data
        $cart = $user->cart;
        $cartItemCount = $cart?->items->sum('quantity') ?? 0;
        $cartTotal = $cart?->items->sum(function($item) {
            return $item->product->price * $item->quantity;
        }) ?? 0;
        
        $budget = $user->budget;
        $budgetAmount = $budget?->amount ?? 0;
        $budgetPercentage = $budget && $budgetAmount > 0 ? min(100, round(($cartTotal / $budgetAmount) * 100)) : 0;
        $budgetRemaining = $budget ? max(0, $budgetAmount - $cartTotal) : 0;
        
        $ordersCount = $user->orders()->count();
        $recentOrders = $user->orders()->latest()->take(5)->get();
        
        return view('user.dashboard', compact(
            'cartItemCount', 'cartTotal', 'budget', 'budgetAmount', 
            'budgetPercentage', 'budgetRemaining', 'ordersCount', 'recentOrders'
        ));
    }
    
    public function sellerDashboard()
    {
        if (!Auth::user()->isSeller()) {
            return redirect()->route('dashboard');
        }
        
        return view('seller.dashboard');
    }
}