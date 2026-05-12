<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Cart - SmartCart</title>
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
        
        /* Cart Container */
        .cart-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .cart-header {
            background: #f9fafb;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .cart-header h1 {
            font-size: 1.5rem;
            color: #1f2937;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cart-table th {
            text-align: left;
            padding: 1rem 1.5rem;
            background: #f9fafb;
            color: #6b7280;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .cart-table td {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            background: #f3f4f6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .product-name {
            font-weight: 600;
            color: #1f2937;
        }
        
        .quantity-input {
            width: 80px;
            padding: 0.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            text-align: center;
            font-family: 'Inter', sans-serif;
        }
        
        .quantity-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .update-btn {
            background: none;
            border: none;
            color: #667eea;
            cursor: pointer;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }
        
        .remove-btn {
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .cart-summary {
            background: #f9fafb;
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .total-label {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .total-amount {
            font-size: 1.8rem;
            font-weight: 800;
            color: #667eea;
        }
        
        .empty-cart {
            text-align: center;
            padding: 4rem;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            padding: 0.7rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
        }
        
        .btn-checkout {
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
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
                <a href="{{ route('cart.index') }}" class="active">
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
            
            @if(session('error'))
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
            
            <div class="cart-container">
                <div class="cart-header">
                    <h1><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
                </div>
                
                @if($cartItems->count() > 0)
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                                @php
                                    $subtotal = $item->product->price * $item->quantity;
                                @endphp
                                <tr data-cart-item-id="{{ $item->id }}">
                                    <td>
                                        <div class="product-info">
                                            <div class="product-image">
                                                <i class="fas fa-box"></i>
                                            </div>
                                            <div>
                                                <div class="product-name">{{ $item->product->name }}</div>
                                                <small style="color: #6b7280;">Seller: {{ $item->product->seller->name ?? 'Unknown' }}</small>
                                                @if($item->product->quantity < $item->quantity)
                                                    <div style="color: #ef4444; font-size: 0.75rem;">
                                                        ⚠️ Only {{ $item->product->quantity }} left in stock
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>₹{{ number_format($item->product->price, 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="update-form">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" 
                                                   min="1" max="{{ $item->product->quantity }}" class="quantity-input">
                                            <button type="submit" class="update-btn">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="item-subtotal">₹{{ number_format($subtotal, 2) }}</td>
                                    <td>
                                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="remove-btn" onclick="return confirm('Remove this item?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="cart-summary">
                        <div>
                            <span class="total-label">Total Amount:</span>
                            <span class="total-amount cart-total">₹{{ number_format($total, 2) }}</span>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary" onclick="return confirm('Clear entire cart?')">
                                    <i class="fas fa-trash-alt"></i> Clear Cart
                                </button>
                            </form>
                            <a href="#" class="btn-checkout" id="checkoutBtn">
                                <i class="fas fa-credit-card"></i> Proceed to Checkout
                            </a>
                        </div>
                    </div>
                @else
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #d1d5db;"></i>
                        <h3 style="margin: 1rem 0; color: #6b7280;">Your cart is empty!</h3>
                        <p>Looks like you haven't added any items yet.</p>
                        <a href="{{ route('products.index') }}" class="btn-secondary" style="display: inline-block; margin-top: 1rem;">
                            <i class="fas fa-store"></i> Continue Shopping
                        </a>
                    </div>
                @endif
            </div>
        </main>
    </div>
    
    <script>
        document.querySelectorAll('.update-form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                const url = form.action;
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const row = form.closest('tr');
                        const subtotalCell = row.querySelector('.item-subtotal');
                        subtotalCell.textContent = '₹' + data.item_total.toFixed(2);
                        document.querySelector('.cart-total').textContent = '₹' + data.cart_total.toFixed(2);
                        showToast(data.message);
                        setTimeout(() => location.reload(), 500);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            });
        });
        
        function showToast(message) {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed; bottom: 20px; right: 20px; 
                background: #10b981; color: white; padding: 12px 24px;
                border-radius: 8px; z-index: 9999;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        }
    </script>
</body>
</html>