<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Budget Tracker - SmartCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
        
        /* Budget Content */
        .budget-overview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
        }
        
        .budget-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .budget-amount {
            font-size: 3rem;
            font-weight: 800;
            margin: 1rem 0;
        }
        
        .progress-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .progress-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .stat-box {
            text-align: center;
            flex: 1;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 16px;
        }
        
        .stat-label {
            font-size: 0.85rem;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }
        
        .progress-bar-container {
            background: #e5e7eb;
            border-radius: 12px;
            height: 24px;
            overflow: hidden;
            margin: 1.5rem 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #667eea, #ef4444);
            border-radius: 12px;
            transition: width 0.5s ease;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 10px;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .alert-card {
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .alert-safe { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .alert-warning { background: #fed7aa; color: #92400e; border-left: 4px solid #f59e0b; }
        .alert-critical { background: #fecaca; color: #991b1b; border-left: 4px solid #ef4444; }
        .alert-exceeded { background: #fee2e2; color: #991b1b; border-left: 4px solid #dc2626; }
        .alert-no-budget { background: #e0e7ff; color: #3730a3; border-left: 4px solid #6366f1; }
        
        .budget-form-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        
        .budget-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1.2rem;
            font-family: 'Inter', sans-serif;
        }
        
        .budget-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        
        .insights-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
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
            .progress-stats {
                flex-direction: column;
            }
            .budget-amount {
                font-size: 2rem;
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
            @if(session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert-card" style="background: #fed7aa; color: #92400e;">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                </div>
            @endif
            
            @if(session('info'))
                <div class="alert-info">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                </div>
            @endif
            
            <!-- Budget Overview -->
            <div class="budget-overview">
                <div class="budget-title">
                    <i class="fas fa-chart-line"></i> Your Budget Overview
                </div>
                @if($budget)
                    <div class="budget-amount">
                        ₹{{ number_format($budget->amount, 2) }}
                    </div>
                    <div>Monthly spending limit</div>
                @else
                    <div class="budget-amount">
                        Not Set
                    </div>
                    <div>Set your monthly budget to start tracking</div>
                @endif
            </div>
            
            <!-- Progress Section -->
            <div class="progress-section">
                <h3 style="margin-bottom: 1.5rem;">
                    <i class="fas fa-chart-simple"></i> Real-time Budget Status
                </h3>
                
                <div class="progress-stats">
                    <div class="stat-box">
                        <div class="stat-label">Cart Total</div>
                        <div class="stat-value" id="cartTotal">₹{{ number_format($cartTotal, 2) }}</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Budget Used</div>
                        <div class="stat-value" id="percentageUsed">
                            @if($budget && $budget->amount > 0)
                                {{ round(($cartTotal / $budget->amount) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Remaining</div>
                        <div class="stat-value" id="remainingAmount">
                            @if($budget)
                                ₹{{ number_format(max(0, $budget->amount - $cartTotal), 2) }}
                            @else
                                Not set
                            @endif
                        </div>
                    </div>
                </div>
                
                @php
                    $percentage = $budget && $budget->amount > 0 ? min(100, ($cartTotal / $budget->amount) * 100) : 0;
                @endphp
                
                <div class="progress-bar-container">
                    <div class="progress-fill" id="progressFill" 
                         style="width: {{ $percentage }}%; background: linear-gradient(90deg, #10b981, #667eea, #ef4444);">
                        @if($percentage > 15) {{ round($percentage) }}% @endif
                    </div>
                </div>
                
                <div id="alertContainer">
                    @if($budgetStatus['alert_message'])
                        <div class="alert-card alert-{{ $budgetStatus['alert_level'] == 'exceeded' ? 'exceeded' : ($budgetStatus['alert_level'] == 'critical' ? 'critical' : ($budgetStatus['alert_level'] == 'warning' ? 'warning' : 'safe')) }}">
                            <i class="fas {{ $budgetStatus['is_exceeded'] ? 'fa-skull-crosswalk' : ($budgetStatus['percentage'] >= 70 ? 'fa-chart-line' : 'fa-smile') }}"></i>
                            <div>
                                <strong>{{ $budgetStatus['alert_level'] == 'exceeded' ? 'Budget Exceeded!' : ($budgetStatus['alert_level'] == 'critical' ? 'Critical!' : ($budgetStatus['alert_level'] == 'warning' ? 'Warning!' : 'On Track')) }}</strong>
                                <div>{{ $budgetStatus['alert_message'] }}</div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="action-buttons">
                    <a href="{{ route('cart.index') }}" class="btn-secondary">
                        <i class="fas fa-shopping-cart"></i> View Cart
                    </a>
                    <a href="{{ route('budget.insights') }}" class="btn-secondary">
                        <i class="fas fa-chart-line"></i> View Insights
                    </a>
                </div>
            </div>
            
            <!-- Budget Setting Form -->
            <div class="budget-form-card">
                <h3 style="margin-bottom: 1.5rem;">
                    <i class="fas fa-pen"></i> 
                    {{ $budget ? 'Update Your Budget' : 'Set Your Monthly Budget' }}
                </h3>
                
                <form method="POST" action="{{ route('budget.store') }}" id="budgetForm">
                    @csrf
                    
                    <div class="form-group">
                        <label for="amount">Monthly Budget Amount (₹)</label>
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               class="budget-input" 
                               value="{{ $budget ? $budget->amount : '' }}"
                               placeholder="e.g., 5000"
                               step="0.01"
                               min="0"
                               required>
                        <small style="color: #6b7280; display: block; margin-top: 0.5rem;">
                            Set a realistic budget to help track your spending habits.
                        </small>
                    </div>
                    
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> 
                        {{ $budget ? 'Update Budget' : 'Set Budget' }}
                    </button>
                </form>
            </div>
            
            <div class="insights-link">
                <a href="{{ route('budget.insights') }}" style="color: #667eea; text-decoration: none;">
                    <i class="fas fa-chart-pie"></i> View detailed spending insights →
                </a>
            </div>
        </main>
    </div>
    
    <script>
        function updateBudgetStatus() {
            fetch('{{ route("budget.status") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('cartTotal').innerHTML = '₹' + data.cart_total;
                        document.getElementById('percentageUsed').innerHTML = data.percentage + '%';
                        document.getElementById('remainingAmount').innerHTML = '₹' + data.remaining;
                        
                        const progressFill = document.getElementById('progressFill');
                        const percentage = Math.min(100, parseFloat(data.percentage));
                        progressFill.style.width = percentage + '%';
                        if (percentage > 15) {
                            progressFill.innerHTML = Math.round(percentage) + '%';
                        } else {
                            progressFill.innerHTML = '';
                        }
                        
                        const alertContainer = document.getElementById('alertContainer');
                        if (data.alert_message) {
                            let alertClass = '';
                            let icon = '';
                            
                            if (data.is_exceeded) {
                                alertClass = 'alert-exceeded';
                                icon = 'fa-skull-crosswalk';
                            } else if (data.percentage >= 90) {
                                alertClass = 'alert-critical';
                                icon = 'fa-chart-line';
                            } else if (data.percentage >= 70) {
                                alertClass = 'alert-warning';
                                icon = 'fa-chart-line';
                            } else {
                                alertClass = 'alert-safe';
                                icon = 'fa-smile';
                            }
                            
                            let title = '';
                            if (data.is_exceeded) title = 'Budget Exceeded!';
                            else if (data.percentage >= 90) title = 'Critical!';
                            else if (data.percentage >= 70) title = 'Warning!';
                            else title = 'On Track';
                            
                            alertContainer.innerHTML = `
                                <div class="alert-card ${alertClass}">
                                    <i class="fas ${icon}"></i>
                                    <div>
                                        <strong>${title}</strong>
                                        <div>${data.alert_message}</div>
                                    </div>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }
        
        setInterval(updateBudgetStatus, 30000);
        
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                updateBudgetStatus();
            }
        });
        
        document.getElementById('budgetForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        });
    </script>
</body>
</html>