<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seller Dashboard - SmartCart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        
        /* Stats Cards */
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
        
        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
        }
        
        /* Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        /* Recent Products Table */
        .recent-products {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .products-table th,
        .products-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .products-table th {
            color: #6b7280;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .product-image-small {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .status-low-stock {
            background: #fed7aa;
            color: #92400e;
        }
        
        .status-out-stock {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-in-stock {
            background: #d1fae5;
            color: #065f46;
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
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="{{ route('seller.dashboard') }}" class="sidebar-logo">
                🛒 SmartCart <span style="font-size: 0.8rem;">Seller</span>
            </a>
            
            <nav class="sidebar-nav">
                <a href="{{ route('seller.dashboard') }}" class="active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}">
                    <i class="fas fa-boxes"></i> All Products
                </a>
                <a href="{{ route('products.create') }}">
                    <i class="fas fa-plus-circle"></i> Add Product
                </a>
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-store"></i> View Store
                </a>
            </nav>
            
            <div class="user-info-sidebar">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.2rem;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 600;">{{ Auth::user()->name }}</div>
                        <div style="font-size: 0.75rem; color: #667eea;">Seller Account</div>
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
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h1 class="text-2xl font-bold mb-2">Seller Dashboard 👑</h1>
                <p>Manage your products, track orders, and grow your business with SmartCart.</p>
            </div>
            
            <!-- Stats Cards -->
            @php
                $seller = Auth::user();
                $totalProducts = $seller->products()->count();
                $lowStockProducts = $seller->products()->where('quantity', '<=', 5)->where('quantity', '>', 0)->count();
                $outOfStockProducts = $seller->products()->where('quantity', 0)->count();
                $totalValue = $seller->products()->sum(\DB::raw('price * quantity'));
                $ordersReceived = \App\Models\OrderItem::whereHas('product', function($q) use ($seller) {
                    $q->where('seller_id', $seller->id);
                })->count();
                $totalEarnings = \App\Models\OrderItem::whereHas('product', function($q) use ($seller) {
                    $q->where('seller_id', $seller->id);
                })->sum(\DB::raw('price * quantity'));
            @endphp
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e0e7ff; color: #4f46e5;">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-value">{{ $totalProducts }}</div>
                    <div class="stat-label">Total Products</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-value">{{ $lowStockProducts }}</div>
                    <div class="stat-label">Low Stock (≤5)</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fee2e2; color: #dc2626;">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-value">{{ $outOfStockProducts }}</div>
                    <div class="stat-label">Out of Stock</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-value">₹{{ number_format($totalValue, 2) }}</div>
                    <div class="stat-label">Inventory Value</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value">{{ $ordersReceived }}</div>
                    <div class="stat-label">Orders Received</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #f3e8ff; color: #9333ea;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-value">₹{{ number_format($totalEarnings, 2) }}</div>
                    <div class="stat-label">Total Earnings</div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('products.create') }}" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Add New Product
                </a>
                <a href="{{ route('products.index') }}" class="btn-secondary">
                    <i class="fas fa-boxes"></i> Manage All Products
                </a>
                <a href="{{ route('dashboard') }}" class="btn-secondary">
                    <i class="fas fa-store"></i> View Store Front
                </a>
            </div>
            
            <!-- Recent Products Section -->
            @php $recentProducts = $seller->products()->latest()->take(5)->get(); @endphp
            @if($recentProducts->count() > 0)
            <div class="recent-products">
                <h3 style="font-size: 1.2rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-clock" style="color: #667eea;"></i> Recently Added Products
                </h3>
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentProducts as $product)
                        <tr>
                            <td>
                                <img src="{{ $product->image_url }}" class="product-image-small" alt="{{ $product->name }}">
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                <br>
                                <small style="color: #9ca3af;">ID: #{{ $product->id }}</small>
                            </td>
                            <td>₹{{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->quantity }} units</td>
                            <td>
                                @if($product->quantity == 0)
                                    <span class="status-badge status-out-stock">
                                        <i class="fas fa-times-circle"></i> Out of Stock
                                    </span>
                                @elseif($product->quantity <= 5)
                                    <span class="status-badge status-low-stock">
                                        <i class="fas fa-exclamation-triangle"></i> Low Stock
                                    </span>
                                @else
                                    <span class="status-badge status-in-stock">
                                        <i class="fas fa-check-circle"></i> In Stock
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('products.edit', $product) }}" style="color: #f59e0b; text-decoration: none;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this product?')" style="color: #ef4444; background: none; border: none; cursor: pointer;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($totalProducts > 5)
                <div style="margin-top: 1rem; text-align: center;">
                    <a href="{{ route('products.index') }}" style="color: #667eea; text-decoration: none;">
                        View All Products <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Quick Tips -->
            <div style="background: #fef3c7; border-radius: 16px; padding: 1rem; margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center;">
                <i class="fas fa-lightbulb" style="font-size: 2rem; color: #d97706;"></i>
                <div>
                    <strong style="color: #92400e;">Seller Tips:</strong>
                    <p style="color: #78350f; font-size: 0.85rem; margin-top: 4px;">
                        Keep your products in stock to maximize sales. Add high-quality images to attract more buyers.
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>