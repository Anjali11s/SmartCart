<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Budget Insights - SmartCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; }
        
        /* Dashboard Layout */
        .dashboard-container { display: flex; min-height: 100vh; }
        
        /* Sidebar Styles */
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
        
        /* Insights Content */
        .insights-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .insights-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #1f2937;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border-radius: 16px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #667eea;
        }
        
        canvas {
            max-height: 300px;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
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
                <a href="{{ route('dashboard') }}">
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
                <a href="{{ route('budget.index') }}" class="active">
                    <i class="fas fa-wallet"></i> Budget Tracker
                </a>
                <a href="{{ route('profile.edit') }}">
                    <i class="fas fa-user"></i> Profile Settings
                </a>
            </nav>
            
            <!-- Budget Sidebar Widget -->
            @php
                $budgetWidget = Auth::user()->budget;
                $cartTotalWidget = Auth::user()->cart?->items->sum(fn($i) => $i->product->price * $i->quantity) ?? 0;
            @endphp
            @if($budgetWidget)
            <div class="budget-sidebar">
                <div style="font-size: 0.7rem; opacity: 0.8;">Monthly Budget</div>
                <div style="font-size: 1.2rem; font-weight: bold;">₹{{ number_format($budgetWidget->amount) }}</div>
                <div style="font-size: 0.7rem; margin-top: 5px;">
                    Remaining: ₹{{ number_format(max(0, $budgetWidget->amount - $cartTotalWidget)) }}
                </div>
                <div style="height: 4px; background: rgba(255,255,255,0.3); border-radius: 2px; margin-top: 8px;">
                    <div style="width: {{ min(100, ($cartTotalWidget / $budgetWidget->amount) * 100) }}%; height: 4px; background: white; border-radius: 2px;"></div>
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
            @if(session('info'))
                <div class="alert-info">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                </div>
            @endif
            
            <div class="insights-card">
                <h2 class="insights-title">
                    <i class="fas fa-chart-pie"></i> Your Spending Insights
                </h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">₹{{ number_format($budget->amount, 2) }}</div>
                        <div>Monthly Budget</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">₹{{ number_format($totalSpent, 2) }}</div>
                        <div>Total Spent (All Time)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">₹{{ number_format($averageSpent, 2) }}</div>
                        <div>Average per Order</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">{{ $orders->count() }}</div>
                        <div>Completed Orders</div>
                    </div>
                </div>
                
                <h3 style="margin: 2rem 0 1rem;">Monthly Spending Trend</h3>
                <canvas id="spendingChart"></canvas>
            </div>
            
            <div class="insights-card">
                <h3><i class="fas fa-clock"></i> Recent Orders</h3>
                @if($orders->count() > 0)
                    <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                        <thead>
                            <tr style="background: #f9fafb;">
                                <th style="padding: 0.75rem; text-align: left;">Order ID</th>
                                <th style="padding: 0.75rem; text-align: left;">Date</th>
                                <th style="padding: 0.75rem; text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr style="border-bottom: 1px solid #e5e7eb;">
                                    <td style="padding: 0.75rem;">#{{ $order->id }}</td>
                                    <td style="padding: 0.75rem;">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td style="padding: 0.75rem; text-align: right;">₹{{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="color: #6b7280; text-align: center; padding: 2rem;">
                        No completed orders yet. Start shopping to see insights!
                    </p>
                @endif
            </div>
            
            <div style="text-align: center;">
                <a href="{{ route('budget.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Budget Tracker
                </a>
            </div>
        </main>
    </div>
    
    <script>
        const ctx = document.getElementById('spendingChart').getContext('2d');
        const monthlyData = @json($monthlySpending);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [{
                    label: 'Monthly Spending (₹)',
                    data: monthlyData.map(item => item.total),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (₹)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>