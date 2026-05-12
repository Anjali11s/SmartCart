<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Product - SmartCart</title>
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
        
        /* Form Styles */
        .form-container {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-title {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
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
        
        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        
        .error-message ul {
            margin-left: 1.5rem;
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
            <a href="{{ route('seller.dashboard') }}" class="sidebar-logo">
                🛒 SmartCart <span style="font-size: 0.8rem;">Seller</span>
            </a>
            
            <nav class="sidebar-nav">
                <a href="{{ route('seller.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('products.index') }}">
                    <i class="fas fa-boxes"></i> All Products
                </a>
                <a href="{{ route('products.create') }}" class="active">
                    <i class="fas fa-plus-circle"></i> Add Product
                </a>
                <a href="{{ route('dashboard') }}">
                    <i class="fas fa-store"></i> View Store
                </a>
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
            <div class="form-container">
                <h1 class="form-title">
                    <i class="fas fa-plus-circle"></i> Add New Product
                </h1>
                
                @if($errors->any())
                    <div class="error-message">
                        <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
                        <ul class="mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label for="name">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" placeholder="Enter product name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price (₹) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" class="form-control" placeholder="0.00" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity (Stock) <span class="text-red-500">*</span></label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" class="form-control" placeholder="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" name="image" id="image" accept="image/*" class="form-control" style="padding: 0.5rem;">
                        <small class="text-gray-500 mt-1 block">Accepted formats: JPEG, PNG, JPG (Max: 2MB)</small>
                    </div>
                    
                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Create Product
                        </button>
                        <a href="{{ route('products.index') }}" class="btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>