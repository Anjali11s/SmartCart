<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartCart - Smart Shopping Cart with Budget Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        }
        
        .logo {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .logo span {
            font-size: 0.85rem;
            background: none;
            -webkit-text-fill-color: #6b7280;
            font-weight: 400;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #667eea;
        }
        
        .btn-login {
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border-radius: 50px;
            transition: all 0.3s !important;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-register {
            padding: 0.5rem 1.5rem;
            border: 2px solid #667eea;
            color: #667eea !important;
            border-radius: 50px;
            background: white;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            background: #667eea;
            color: white !important;
            transform: translateY(-2px);
        }
        
        /* Google Button */
        .btn-google {
            padding: 0.5rem 1.5rem;
            background: white;
            color: #4b5563 !important;
            border: 1px solid #e5e7eb;
            border-radius: 50px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-google:hover {
            background: #f3f4f6;
            transform: translateY(-2px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .btn-google img {
            width: 18px;
            height: 18px;
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7rem 5% 4rem;
            gap: 3rem;
            flex-wrap: wrap;
        }
        
        .hero-content {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 3rem;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: fadeInUp 0.8s ease;
        }
        
        .hero-content h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 1.2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }
        
        .hero-content p {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .features {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        
        .feature {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: #4b5563;
            background: #f9fafb;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 1px solid #e5e7eb;
        }
        
        .feature i {
            color: #667eea;
            font-size: 1rem;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #9ca3af;
            font-size: 0.8rem;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .divider::before {
            margin-right: 1rem;
        }
        
        .divider::after {
            margin-left: 1rem;
        }
        
        .btn-primary {
            padding: 0.9rem 2rem;
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
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            padding: 0.9rem 2rem;
            background: white;
            color: #667eea;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            border-color: #667eea;
            background: #f9fafb;
            transform: translateY(-2px);
        }
        
        /* Animated Aesthetic Design */
        .hero-image {
            flex: 1;
            min-width: 300px;
            animation: fadeInRight 0.8s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .animation-container {
            position: relative;
            width: 380px;
            height: 380px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .gradient-circle {
            width: 280px;
            height: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: 0 0 50px rgba(102, 126, 234, 0.3);
            position: relative;
            z-index: 2;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
        }
        
        .orb {
            position: absolute;
            border-radius: 50%;
            background: white;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            animation: float 4s ease-in-out infinite;
        }
        
        .orb-1 {
            width: 60px;
            height: 60px;
            top: 20px;
            right: 40px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            animation-delay: 0s;
        }
        
        .orb-2 {
            width: 40px;
            height: 40px;
            bottom: 60px;
            left: 30px;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            animation-delay: 1s;
        }
        
        .orb-3 {
            width: 80px;
            height: 80px;
            top: 50%;
            left: -20px;
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            animation-delay: 0.5s;
            opacity: 0.6;
        }
        
        .orb-4 {
            width: 35px;
            height: 35px;
            bottom: 100px;
            right: 20px;
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            animation-delay: 1.5s;
        }
        
        .orb-5 {
            width: 50px;
            height: 50px;
            top: 120px;
            right: -10px;
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
            animation-delay: 0.8s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            25% {
                transform: translateY(-15px) translateX(10px);
            }
            50% {
                transform: translateY(0) translateX(20px);
            }
            75% {
                transform: translateY(15px) translateX(10px);
            }
        }
        
        .ring {
            position: absolute;
            width: 320px;
            height: 320px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: rotate 10s linear infinite;
        }
        
        .ring-2 {
            width: 340px;
            height: 340px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            animation: rotate 15s linear infinite reverse;
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        .center-icon {
            position: absolute;
            z-index: 3;
            font-size: 5rem;
            color: white;
            animation: bounce 2s ease-in-out infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .stats-section {
            padding: 4rem 5%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 40px 40px 0 0;
            margin-top: 2rem;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .stat-item {
            text-align: center;
            flex: 1;
            min-width: 150px;
            padding: 1.5rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            border: 1px solid #e5e7eb;
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-label {
            color: #6b7280;
            margin-top: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .how-it-works {
            padding: 4rem 5%;
            background: white;
        }
        
        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 3rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .steps {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .step {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }
        
        .step:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
        }
        
        .step h3 {
            font-size: 1.2rem;
            margin-bottom: 0.8rem;
            color: #374151;
        }
        
        .step p {
            color: #6b7280;
            font-size: 0.85rem;
            line-height: 1.5;
        }
        
        .footer {
            background: #1f2937;
            color: #9ca3af;
            text-align: center;
            padding: 2rem;
            font-size: 0.8rem;
            margin-top: 3rem;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            .navbar {
                padding: 1rem 5%;
            }
            .logo {
                font-size: 1.2rem;
            }
            .hero-content {
                padding: 1.5rem;
            }
            .step {
                min-width: 250px;
            }
            .animation-container {
                width: 300px;
                height: 300px;
            }
            .gradient-circle {
                width: 200px;
                height: 200px;
            }
            .ring, .ring-2 {
                width: 240px;
                height: 240px;
            }
            .ring-2 {
                width: 260px;
                height: 260px;
            }
            .center-icon {
                font-size: 3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo">
            🛒 SmartCart <span>| Budget Tracker</span>
        </div>
        <div class="nav-links">
            @auth
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="btn-login">Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-login">Sign In</a>
                <a href="{{ route('register') }}" class="btn-register">Get Started</a>
            @endauth
        </div>
    </nav>
    
    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <h1>Smart Shopping Cart <br>with Budget Tracker</h1>
            <p>Shop smarter, track your spending, and never exceed your budget again! SmartCart helps you manage your shopping within your limits.</p>
            
            <div class="features">
                <div class="feature">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Smart Cart</span>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    <span>Budget Tracker</span>
                </div>
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Payments</span>
                </div>
                <div class="feature">
                    <i class="fas fa-tags"></i>
                    <span>Exclusive Deals</span>
                </div>
            </div>
            
            <div class="hero-buttons">
                @guest
                    <a href="{{ route('register') }}" class="btn-primary">
                        <i class="fas fa-user-plus"></i> Get Started Free
                    </a>
                    <a href="{{ route('login') }}" class="btn-secondary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                @else
                    <a href="{{ url('/dashboard') }}" class="btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                    </a>
                @endguest
            </div>
            
            <!-- Google Login Button -->
            @guest
            <div class="divider">or</div>
            <a href="{{ route('login.google') }}" class="btn-google" style="display: flex; align-items: center; justify-content: center; width: 100%;">
                <img src="https://www.google.com/favicon.ico" alt="Google">
                Continue with Google
            </a>
            @endguest
        </div>
        
        <div class="hero-image">
            <div class="animation-container">
                <div class="ring"></div>
                <div class="ring ring-2"></div>
                <div class="gradient-circle"></div>
                <div class="orb orb-1"></div>
                <div class="orb orb-2"></div>
                <div class="orb orb-3"></div>
                <div class="orb orb-4"></div>
                <div class="orb orb-5"></div>
                <div class="center-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Section -->
    <div class="stats-section">
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number">1000+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">98%</div>
                <div class="stat-label">Satisfaction Rate</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
            </div>
        </div>
    </div>
    
    <!-- How It Works Section -->
    <div class="how-it-works">
        <h2 class="section-title">How It Works 🚀</h2>
        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Create Account</h3>
                <p>Sign up for free and set your monthly budget</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Add Products</h3>
                <p>Shop and add items to your smart cart</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Track Budget</h3>
                <p>Watch your spending in real-time</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h3>Save Money</h3>
                <p>Get insights to save more</p>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>© {{ date('Y') }} SmartCart. All rights reserved. | Smart Shopping Cart with Budget Tracker</p>
    </div>
</body>
</html>