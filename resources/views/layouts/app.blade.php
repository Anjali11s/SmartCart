<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SmartCart') }} - Smart Shopping</title>

    <!-- Tailwind + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.02); }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                        🛒 SmartCart
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600">Dashboard</a>
                        <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-indigo-600">Products</a>

                        <!-- Cart Icon with Badge -->
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            @php $cartCount = auth()->user()->cart?->items->sum('quantity') ?? 0; @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">{{ $cartCount }}</span>
                            @endif
                        </a>

                        <!-- Budget Link -->
                        <a href="{{ route('budget.index') }}" class="text-gray-700 hover:text-indigo-600">Budget</a>

                        <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-indigo-600">Orders</a>

                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 focus:outline-none">
                                <div class="w-8 h-8 rounded-full gradient-bg flex items-center justify-center text-white font-bold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <span class="text-gray-700">{{ Auth::user()->name }}</span>
                                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden group-hover:block z-10">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                                @if(Auth::user()->isSeller())
                                    <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Seller Panel</a>
                                @endif
                                @if(Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Admin Panel</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800">Login</a>
                        <a href="{{ route('register') }}" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90 transition">Sign Up</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 text-center text-gray-500 text-sm">
            © {{ date('Y') }} SmartCart – Smart Shopping with Budget Tracker. All rights reserved.
        </div>
    </footer>
</body>
</html>