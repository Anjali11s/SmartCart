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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            margin-top: 0.5rem;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }
        
        .recent-orders {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .orders-table th,
        .orders-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .orders-table th {
            color: #6b7280;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .status-placed { background: #dbeafe; color: #1e40af; }
        .status-dispatched { background: #fed7aa; color: #92400e; }
        .status-delivered { background: #d1fae5; color: #065f46; }
        
        /* Pagination Styles */
        .orders-pagination {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .orders-pagination a,
        .orders-pagination span {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            color: #4b5563;
            background: #f3f4f6;
            transition: all 0.3s;
        }
        
        .orders-pagination a:hover {
            background: #667eea;
            color: white;
        }
        
        .orders-pagination .active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
                transition: transform 0.3s;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .orders-table {
                font-size: 0.75rem;
            }
            .orders-table th,
            .orders-table td {
                padding: 0.5rem;
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
                <a href="{{ route('dashboard') }}" class="active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}">
                    <i class="fas fa-store"></i> Browse Products
                </a>
                <a href="{{ route('cart.index') }}">
                    <i class="fas fa-shopping-cart"></i> My Cart
                    @php $cartCount = Auth::user()->cart?->items->sum('quantity') ?? 0; @endphp
                    @if($cartCount > 0)
                        <span style="background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.7rem; margin-left: auto;">{{ $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('budget.index') }}">
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
            <div class="top-navbar">
                <h1 class="text-xl font-bold">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <div class="text-gray-500">{{ now()->format('l, F j, Y') }}</div>
            </div>
            
            <!-- Stats Cards -->
            @php
                $cart = Auth::user()->cart;
                $cartItemCount = $cart?->items->sum('quantity') ?? 0;
                $cartTotal = $cart?->items->sum(function($item) {
                    return $item->product->price * $item->quantity;
                }) ?? 0;
                $budgetAmount = $budget?->amount ?? 0;
                $budgetPercentage = $budget && $budgetAmount > 0 ? min(100, round(($cartTotal / $budgetAmount) * 100)) : 0;
                $ordersCount = Auth::user()->orders()->count();
                $recentOrders = Auth::user()->orders()->latest()->paginate(5);
            @endphp
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e0e7ff; color: #4f46e5;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value">{{ $cartItemCount }}</div>
                    <div class="stat-label">Items in Cart</div>
                    @if($cartTotal > 0)
                        <div class="stat-label" style="margin-top: 5px;">Total: ₹{{ number_format($cartTotal, 2) }}</div>
                    @endif
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    @if($budget)
                        <div class="stat-value">{{ $budgetPercentage }}%</div>
                        <div class="stat-label">Budget Used</div>
                        <div class="stat-label" style="margin-top: 5px;">Remaining: ₹{{ number_format(max(0, $budgetAmount - $cartTotal)) }}</div>
                    @else
                        <div class="stat-value">Not Set</div>
                        <div class="stat-label">Monthly Budget</div>
                    @endif
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">{{ $ordersCount }}</div>
                    <div class="stat-label">Orders Placed</div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem;">
                <a href="{{ route('products.index') }}" class="btn-primary" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 0.7rem 1.5rem; border-radius: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-store"></i> Browse Products
                </a>
                <a href="{{ route('cart.index') }}" style="background: #f3f4f6; color: #4b5563; padding: 0.7rem 1.5rem; border-radius: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-shopping-cart"></i> View Cart
                </a>
                <a href="{{ route('budget.index') }}" style="background: #f3f4f6; color: #4b5563; padding: 0.7rem 1.5rem; border-radius: 12px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-wallet"></i> Set Budget
                </a>
            </div>
            
            <!-- Recent Orders Section with Pagination -->
            @if($recentOrders->count() > 0)
            <div class="recent-orders">
                <h3 style="font-size: 1.2rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-history" style="color: #667eea;"></i> Recent Orders
                </h3>
                <div class="overflow-x-auto">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->created_at->format('d M Y') }}<br><small style="color: #9ca3af;">{{ $order->created_at->format('h:i A') }}</small></td>
                                <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                                <td>
                                    <span class="status-badge status-{{ $order->order_status }}">
                                        <i class="fas {{ $order->order_status == 'placed' ? 'fa-clock' : ($order->order_status == 'dispatched' ? 'fa-truck' : 'fa-check-circle') }}"></i>
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge" style="background: #f3e8ff; color: #9333ea;">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination Links -->
                <div class="orders-pagination">
                    {{ $recentOrders->links() }}
                </div>
            </div>
            @else
            <div class="recent-orders" style="text-align: center;">
                <i class="fas fa-shopping-bag" style="font-size: 3rem; color: #d1d5db;"></i>
                <p style="color: #6b7280; margin-top: 1rem;">No orders yet. Start shopping to see your orders here!</p>
                <a href="{{ route('products.index') }}" style="display: inline-block; margin-top: 1rem; color: #667eea; text-decoration: none;">Browse Products →</a>
            </div>
            @endif
        </main>
    </div>
</body>
</html>