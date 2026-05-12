<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} - SmartCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        
        .sidebar-nav a:hover {
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
        
        /* Product Details */
        .product-container {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .product-image-main {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
        
        .product-details {
            padding: 2rem;
        }
        
        .product-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .product-price-large {
            font-size: 2rem;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .stock-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            margin: 1rem 0;
        }
        
        .stock-in { background: #d1fae5; color: #065f46; }
        .stock-low { background: #fed7aa; color: #92400e; }
        .stock-out { background: #fee2e2; color: #991b1b; }
        
        .quantity-input {
            width: 100px;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            text-align: center;
        }
        
        .quantity-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-add-to-cart {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }
        
        .btn-add-to-cart:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            padding: 0.7rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
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
            .product-image-main {
                height: 250px;
            }
            .product-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="{{ Auth::user()->isSeller() ? route('seller.dashboard') : route('dashboard') }}" class="sidebar-logo">
                🛒 SmartCart
                @if(Auth::user()->isSeller())
                    <span style="font-size: 0.8rem;">Seller</span>
                @endif
            </a>
            
            <nav class="sidebar-nav">
                @if(Auth::user()->isSeller())
                    <a href="{{ route('seller.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('products.index') }}">
                        <i class="fas fa-boxes"></i> All Products
                    </a>
                    <a href="{{ route('products.create') }}">
                        <i class="fas fa-plus-circle"></i> Add Product
                    </a>
                @else
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('products.index') }}">
                        <i class="fas fa-store"></i> Browse Products
                    </a>
                    <a href="{{ route('cart.index') }}">
                        <i class="fas fa-shopping-cart"></i> My Cart
                    </a>
                    <a href="{{ route('budget.index') }}">
                        <i class="fas fa-wallet"></i> Budget Tracker
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}">
                    <i class="fas fa-user"></i> Profile
                </a>
            </nav>
            
            <div class="user-info-sidebar">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 600;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.75rem; color: #667eea;">{{ ucfirst(Auth::user()->role) }}</div>
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
            <!-- Flash Messages -->
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
            
            <!-- Product Details -->
            <div class="product-container">
                <div class="md:flex">
                    <div class="md:w-1/2">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image-main">
                    </div>
                    <div class="md:w-1/2 product-details">
                        <h1 class="product-title">{{ $product->name }}</h1>
                        <p class="product-price-large">₹{{ number_format($product->price, 2) }}</p>
                        
                        <!-- Stock Status -->
                        @if($product->quantity == 0)
                            <div class="stock-status stock-out">
                                <i class="fas fa-times-circle"></i> Out of Stock
                            </div>
                        @elseif($product->quantity <= 5)
                            <div class="stock-status stock-low">
                                <i class="fas fa-exclamation-triangle"></i> Only {{ $product->quantity }} units left!
                            </div>
                        @else
                            <div class="stock-status stock-in">
                                <i class="fas fa-check-circle"></i> In Stock ({{ $product->quantity }} units)
                            </div>
                        @endif
                        
                        <p style="color: #6b7280; margin: 0.5rem 0;">
                            <i class="fas fa-store"></i> Seller: {{ $product->seller->name ?? 'Admin' }}
                        </p>
                        
                        @if(Auth::user()->isUser() && $product->quantity > 0)
                            <form action="{{ route('cart.add') }}" method="POST" id="addToCartForm" style="margin: 1.5rem 0;">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Quantity:</label>
                                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                    <input type="number" 
                                           name="quantity" 
                                           id="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $product->quantity }}" 
                                           class="quantity-input">
                                    <button type="submit" class="btn-add-to-cart" id="addToCartBtn">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </form>
                        @elseif(Auth::user()->isUser() && $product->quantity == 0)
                            <div class="stock-status stock-out" style="justify-content: center; margin: 1.5rem 0;">
                                <i class="fas fa-times-circle"></i> This product is currently out of stock
                            </div>
                        @endif
                        
                        <!-- Seller/Action Buttons -->
                        @if(Auth::user()->id == $product->seller_id || Auth::user()->isAdmin())
                            <div class="action-buttons">
                                <a href="{{ route('products.edit', $product) }}" class="btn-secondary" style="background: #fef3c7; color: #d97706;">
                                    <i class="fas fa-edit"></i> Edit Product
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this product permanently?')" class="btn-secondary" style="background: #fee2e2; color: #dc2626; border: none; cursor: pointer;">
                                        <i class="fas fa-trash"></i> Delete Product
                                    </button>
                                </form>
                            </div>
                        @endif
                        
                        <!-- Back Buttons -->
                        <div class="action-buttons" style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <a href="{{ route('products.index') }}" class="btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                            <a href="{{ Auth::user()->isSeller() ? route('seller.dashboard') : route('dashboard') }}" class="btn-secondary">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('addToCartBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        });
    </script>
</body>
</html>