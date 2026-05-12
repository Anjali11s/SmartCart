<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>All Products - SmartCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        
        .product-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 1rem;
        }
        
        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            font-size: 1.3rem;
            font-weight: 800;
            color: #667eea;
        }
        
        .stock-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .stock-in { background: #d1fae5; color: #065f46; }
        .stock-low { background: #fed7aa; color: #92400e; }
        .stock-out { background: #fee2e2; color: #991b1b; }
        
        .btn-view {
            background: #f3f4f6;
            color: #4b5563;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            text-decoration: none;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s;
        }
        
        .btn-view:hover {
            background: #e5e7eb;
        }
        
        .btn-edit {
            background: #fef3c7;
            color: #d97706;
        }
        
        .btn-edit:hover {
            background: #fde68a;
        }
        
        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .btn-delete:hover {
            background: #fecaca;
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .pagination {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
        }
        
        /* Loading Skeleton Styles */
        .skeleton-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        
        .skeleton-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        .skeleton-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        .skeleton-content {
            padding: 1rem;
        }
        
        .skeleton-title {
            height: 20px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 4px;
            margin-bottom: 0.5rem;
            animation: shimmer 1.5s infinite;
        }
        
        .skeleton-price {
            height: 24px;
            width: 60%;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 4px;
            margin-bottom: 1rem;
            animation: shimmer 1.5s infinite;
        }
        
        .skeleton-button {
            height: 36px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            border-radius: 8px;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.8;
            }
        }
        
        /* Hide skeleton when content loads */
        .skeleton-container.hide {
            display: none;
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
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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
                    <a href="{{ route('products.index') }}" class="active">
                        <i class="fas fa-boxes"></i> All Products
                    </a>
                    <a href="{{ route('products.create') }}">
                        <i class="fas fa-plus-circle"></i> Add Product
                    </a>
                @else
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('products.index') }}" class="active">
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
            
            <!-- Header -->
            <div class="header-actions">
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-boxes"></i> 
                    {{ Auth::user()->isSeller() ? 'Manage Products' : 'All Products' }}
                </h1>
                @if(Auth::user()->isSeller())
                    <a href="{{ route('products.create') }}" class="btn-primary">
                        <i class="fas fa-plus-circle"></i> Add New Product
                    </a>
                @endif
            </div>
            
            <!-- Loading Skeleton -->
            <div id="skeletonLoader" class="skeleton-container">
                <div class="skeleton-grid">
                    @for($i = 0; $i < 8; $i++)
                    <div class="skeleton-card">
                        <div class="skeleton-image"></div>
                        <div class="skeleton-content">
                            <div class="skeleton-title"></div>
                            <div class="skeleton-price"></div>
                            <div class="skeleton-button"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
            
            <!-- Products Grid (Initially Hidden) -->
            <div id="productsContainer" style="display: none;">
                @if($products->count() > 0)
                    <div class="products-grid">
                        @foreach($products as $product)
                        <div class="product-card">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image">
                            <div class="product-info">
                                <h3 class="product-name">{{ $product->name }}</h3>
                                <p class="product-price">₹{{ number_format($product->price, 2) }}</p>
                                
                                <div style="margin: 0.5rem 0;">
                                    @if($product->quantity == 0)
                                        <span class="stock-badge stock-out">
                                            <i class="fas fa-times-circle"></i> Out of Stock
                                        </span>
                                    @elseif($product->quantity <= 5)
                                        <span class="stock-badge stock-low">
                                            <i class="fas fa-exclamation-triangle"></i> Only {{ $product->quantity }} left
                                        </span>
                                    @else
                                        <span class="stock-badge stock-in">
                                            <i class="fas fa-check-circle"></i> In Stock
                                        </span>
                                    @endif
                                </div>
                                
                                <p style="font-size: 0.75rem; color: #9ca3af; margin-bottom: 1rem;">
                                    <i class="fas fa-store"></i> {{ $product->seller->name ?? 'Admin' }}
                                </p>
                                
                                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <a href="{{ route('products.show', $product) }}" class="btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    @if(Auth::user()->id == $product->seller_id || Auth::user()->isAdmin())
                                        <a href="{{ route('products.edit', $product) }}" class="btn-view btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this product?')" class="btn-view btn-delete" style="border: none; cursor: pointer;">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="pagination">
                        {{ $products->links() }}
                    </div>
                @else
                    <div style="text-align: center; padding: 4rem; background: white; border-radius: 20px;">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: #d1d5db;"></i>
                        <h3 style="margin-top: 1rem; color: #6b7280;">No products found</h3>
                        @if(Auth::user()->isSeller())
                            <a href="{{ route('products.create') }}" class="btn-primary" style="display: inline-block; margin-top: 1rem;">
                                <i class="fas fa-plus-circle"></i> Add Your First Product
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </main>
    </div>
    
    <script>
        // Simulate loading to show skeleton
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                document.getElementById('skeletonLoader').style.display = 'none';
                document.getElementById('productsContainer').style.display = 'block';
            }, 800); // 800ms skeleton display for better UX
        });
    </script>
</body>
</html>