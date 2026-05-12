<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SmartCart') }} - User Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; }
        
        .dashboard-container { display: flex; min-height: 100vh; }
        
        .sidebar {
            width: 280px;
            background: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            padding: 2rem 1.5rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }
        
        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            display: block;
            text-decoration: none;
        }
        
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .sidebar-nav a {
            padding: 0.75rem 1rem;
            border-radius: 12px;
            color: #4b5563;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
            color: #667eea;
        }
        
        .sidebar-nav a i {
            width: 20px;
        }
        
        .user-info-sidebar {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .budget-sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 1rem;
            margin-top: 1rem;
            color: white;
        }
        
        .top-navbar {
            background: white;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="{{ route('dashboard') }}" class="sidebar-logo">🛒 SmartCart</a>
            
            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i> Browse Products
                </a>
                <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i> My Cart
                    @php $cartCount = Auth::user()->cart?->items->sum('quantity') ?? 0; @endphp
                    @if($cartCount > 0)
                        <span style="background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; margin-left: auto;">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('budget.index') }}" class="{{ request()->routeIs('budget.*') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i> Budget Tracker
                </a>
                <a href="{{ route('profile.edit') }}">
                    <i class="fas fa-user"></i> Profile Settings
                </a>
            </nav>
            
            @php
                $budget = Auth::user()->budget;
                $cartTotal = Auth::user()->cart?->items->sum(fn($i) => $i->product->price * $i->quantity) ?? 0;
            @endphp
            @if($budget)
            <div class="budget-sidebar">
                <div style="font-size: 0.7rem; opacity: 0.8;">Monthly Budget</div>
                <div style="font-size: 1.2rem; font-weight: bold;">₹{{ number_format($budget->amount) }}</div>
                <div style="font-size: 0.7rem; margin-top: 5px;">
                    Remaining: ₹{{ number_format(max(0, $budget->amount - $cartTotal)) }}
                </div>
                <div style="height: 4px; background: rgba(255,255,255,0.3); border-radius: 2px; margin-top: 8px;">
                    <div style="width: {{ min(100, ($cartTotal / $budget->amount) * 100) }}%; height: 4px; background: white; border-radius: 2px;"></div>
                </div>
            </div>
            @endif
            
            <div class="user-info-sidebar">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 600;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.7rem; color: #667eea;">{{ ucfirst(Auth::user()->role) }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 0.6rem; background: #fee2e2; color: #dc2626; border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>