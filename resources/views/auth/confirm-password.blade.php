<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartCart - Confirm Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .confirm-container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 450px;
            width: 100%;
            padding: 50px 40px;
            text-align: center;
        }
        .logo {
            margin-bottom: 25px;
        }
        .logo h2 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .security-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #667eea;
        }
        h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #1f2937;
        }
        .description {
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
            font-size: 0.9rem;
        }
        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 0.85rem;
        }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        .input-icon input {
            width: 100%;
            padding: 13px 16px 13px 45px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .input-icon input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
        }
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        .error-text {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 5px;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            text-align: left;
        }
        .footer-links {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .footer-links a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="confirm-container">
        <div class="logo">
            <h2>🛒 SmartCart</h2>
        </div>
        
        <div class="security-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        
        <h2>Confirm Password</h2>
        <p class="description">This is a secure area. Please confirm your password before continuing.</p>
        
        @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                    <i class="fas fa-exclamation-circle"></i> {{ $error }}<br>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf
            
            <div class="form-group">
                <label for="password">Your Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
            </div>
            
            <button type="submit" class="btn-primary">
                <i class="fas fa-check-circle"></i> Confirm
            </button>
        </form>
        
        <div class="footer-links">
            <a href="{{ url('/') }}">
                <i class="fas fa-home"></i> Back to Home
            </a>
        </div>
    </div>
    
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>