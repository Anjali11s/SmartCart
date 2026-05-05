<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartCart - Login</title>
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
        .login-container {
            background: white;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-wrap: wrap;
        }
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            animation: pulse 20s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.5; }
        }
        .login-left h1 { font-size: 2.8rem; font-weight: 800; margin-bottom: 20px; position: relative; z-index: 1; }
        .login-left p { font-size: 1rem; line-height: 1.5; opacity: 0.9; position: relative; z-index: 1; }
        .feature-list { margin-top: 40px; position: relative; z-index: 1; }
        .feature-item { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .feature-item i { width: 24px; font-size: 1.2rem; }
        .login-right { flex: 1; padding: 50px 45px; background: white; }
        .logo { text-align: center; margin-bottom: 35px; }
        .logo h2 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .logo p { color: #6b7280; font-size: 0.85rem; margin-top: 8px; }
        .alert { padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 3px solid #ef4444; }
        .alert-success { background: #d1fae5; color: #065f46; border-left: 3px solid #10b981; }
        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 0.85rem; }
        
        /* Password Field - FIXED: Single eye button always visible, no duplication */
        .password-container {
            position: relative;
            width: 100%;
        }
        .password-container input {
            width: 100%;
            padding: 13px 45px 13px 45px; /* Consistent padding: left for lock, right for eye */
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }
        .password-container input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .password-container .left-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1rem;
            pointer-events: none;
            z-index: 2;
        }
        /* SINGLE TOGGLE EYE - Always visible, no duplication */
        .password-container .toggle-eye {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 1.1rem;
            transition: all 0.2s ease;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            z-index: 2;
        }
        .password-container .toggle-eye:hover {
            color: #667eea;
        }
        
        /* Email Field */
        .email-container {
            position: relative;
            width: 100%;
        }
        .email-container input {
            width: 100%;
            padding: 13px 16px 13px 45px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .email-container input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .email-container .left-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .checkbox { display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .checkbox input { width: 16px; height: 16px; cursor: pointer; accent-color: #667eea; }
        .checkbox span { color: #6b7280; font-size: 0.85rem; cursor: pointer; }
        .forgot-link { color: #667eea; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; }
        .forgot-link:hover { color: #764ba2; text-decoration: underline; }
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .login-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4); }
        .login-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .register-link { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .register-link p { color: #6b7280; font-size: 0.85rem; }
        .register-link a { color: #667eea; text-decoration: none; font-weight: 600; }
        .register-link a:hover { color: #764ba2; }
        
        /* Back to Home Button - HIGHLIGHTED */
        .back-home {
            text-align: center;
            margin-top: 20px;
        }
        .back-home a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f3f4f6;
            color: #4b5563;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .back-home a:hover {
            background: #e5e7eb;
            color: #667eea;
            transform: translateX(-3px);
        }
        
        .secure-footer { text-align: center; margin-top: 25px; color: #9ca3af; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .shake { animation: shake 0.3s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        @media (max-width: 768px) { .login-left { display: none; } .login-right { padding: 35px 25px; } }
        
        /* Additional fix: ensure input padding never overlaps icons */
        .password-container input::-ms-reveal,
        .password-container input::-ms-clear {
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <h1>Welcome Back! 👋</h1>
            <p>Sign in to access your SmartCart account and continue your smart shopping journey.</p>
            <div class="feature-list">
                <div class="feature-item"><i class="fas fa-shopping-cart"></i><span>Smart Shopping Cart</span></div>
                <div class="feature-item"><i class="fas fa-chart-line"></i><span>Budget Tracker</span></div>
                <div class="feature-item"><i class="fas fa-shield-alt"></i><span>Secure Payments</span></div>
                <div class="feature-item"><i class="fas fa-tags"></i><span>Exclusive Deals</span></div>
            </div>
        </div>

        <div class="login-right">
            <div class="logo">
                <h2>🛒 SmartCart</h2>
                <p>Sign in to your account</p>
            </div>

            <div id="alertContainer"></div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="email-container">
                        <i class="fas fa-envelope left-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
                    </div>
                </div>

                <!-- Password Field - FIXED: Single eye button only, no duplicate appears when typing -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <i class="fas fa-lock left-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <!-- SINGLE EYE BUTTON - Fixed position, always one, no duplication -->
                        <i class="fas fa-eye toggle-eye" id="togglePassword"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                    @endif
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>

            <div class="register-link">
                <p>Don't have an account? <a href="{{ route('register') }}">Create Account</a></p>
            </div>

            <!-- Back to Home - HIGHLIGHTED BUTTON -->
            <div class="back-home">
                <a href="{{ url('/') }}">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>

            <div class="secure-footer">
                <i class="fas fa-shield-alt"></i>
                <span>Secure login with SSL encryption</span>
            </div>
        </div>
    </div>

    <script>
        // ========================
        // FIXED: SINGLE EYE TOGGLE - NO DUPLICATE, NO EXTRA BUTTON ON TYPING
        // ========================
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        // Ensure the eye icon toggles password visibility without creating any duplicate button
        if (togglePassword && passwordInput) {
            // Remove any existing event listeners by replacing with a clean one
            togglePassword.removeEventListener('click', togglePassword._listener);
            
            const handleToggle = function(e) {
                e.preventDefault();
                e.stopPropagation();  // Prevent any unwanted bubbling
                
                // Toggle input type between 'password' and 'text'
                const currentType = passwordInput.getAttribute('type');
                const newType = currentType === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', newType);
                
                // Toggle eye icon classes
                if (newType === 'text') {
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            };
            
            // Store reference and attach event
            togglePassword._listener = handleToggle;
            togglePassword.addEventListener('click', handleToggle);
            
            // Additional fix: Prevent browser's native password manager from adding its own icon
            // This ensures no duplicate eye button appears when typing
            passwordInput.style.setProperty('padding-right', '45px', 'important');
            
            // Also handle any dynamic changes: Prevents any potential duplication if DOM gets modified
            // But since we have only one static eye, it's safe.
        }

        // Form Validation with enhanced error handling
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('loginBtn');
        const emailInput = document.getElementById('email');
        const alertContainer = document.getElementById('alertContainer');

        function showAlert(message, type = 'error') {
            // Clear any existing alerts that are not removed yet (optional but keeps UI clean)
            const existingAlerts = alertContainer.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            const icon = type === 'error' ? '<i class="fas fa-exclamation-circle"></i>' : '<i class="fas fa-check-circle"></i>';
            alertDiv.innerHTML = `${icon} ${message}`;
            alertContainer.appendChild(alertDiv);
            
            // Auto-remove after 4 seconds
            setTimeout(() => { 
                if (alertDiv.parentNode) alertDiv.remove(); 
            }, 4000);
        }

        // Real-time validation to remove border red on focus
        function removeFieldErrorBorder(field) {
            field.style.borderColor = '';
        }

        emailInput.addEventListener('focus', () => removeFieldErrorBorder(emailInput));
        passwordInput.addEventListener('focus', () => removeFieldErrorBorder(passwordInput));
        
        // Additional check for any accidental duplicate eye creation from browser extensions or something else
        // Monitor mutations only if needed - but to ensure robust solution, we observe if any extra .toggle-eye appears inside password-container
        // This guarantees that even if something weird happens (like third-party script), we remove duplicates.
        const passwordContainer = document.querySelector('.password-container');
        if (passwordContainer && !window._eyeObserverActive) {
            window._eyeObserverActive = true;
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        // Find all elements with class 'toggle-eye' inside container
                        const allEyes = passwordContainer.querySelectorAll('.toggle-eye');
                        if (allEyes.length > 1) {
                            // Keep only the first eye (original) and remove extras
                            for (let i = 1; i < allEyes.length; i++) {
                                if (allEyes[i] && allEyes[i].parentNode) {
                                    allEyes[i].parentNode.removeChild(allEyes[i]);
                                }
                            }
                        }
                        // Also ensure that no additional eye buttons get injected by browsers (like Edge's password reveal)
                        // This is just a safety net.
                        const inputs = passwordContainer.querySelectorAll('input');
                        inputs.forEach(input => {
                            if (input.type === 'password' || input.type === 'text') {
                                // Force consistent padding to avoid layout shift
                                input.style.paddingRight = '45px';
                            }
                        });
                    }
                });
            });
            observer.observe(passwordContainer, { childList: true, subtree: true });
        }

        // Form submit validation
        form.addEventListener('submit', function(e) {
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            let hasError = false;
            
            // Reset border colors
            emailInput.style.borderColor = '';
            passwordInput.style.borderColor = '';
            
            // Email validation
            if (!email) {
                e.preventDefault();
                showAlert('Please enter your email address');
                emailInput.style.borderColor = '#ef4444';
                emailInput.focus();
                hasError = true;
            } else if (!email.includes('@') || !email.includes('.') || email.length < 3) {
                e.preventDefault();
                showAlert('Please enter a valid email address (e.g., name@example.com)');
                emailInput.style.borderColor = '#ef4444';
                emailInput.focus();
                hasError = true;
            }
            
            // Password validation
            if (!password) {
                if (!hasError) e.preventDefault();
                showAlert('Please enter your password');
                passwordInput.style.borderColor = '#ef4444';
                if (!hasError) passwordInput.focus();
                hasError = true;
            }
            
            // If no errors, show loading state
            if (!hasError) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                // Note: Form will submit naturally to Laravel route
            } else {
                // Add shake effect for error feedback
                const loginRight = document.querySelector('.login-right');
                if (loginRight) {
                    loginRight.classList.add('shake');
                    setTimeout(() => loginRight.classList.remove('shake'), 400);
                }
            }
        });
        
        // Optional: Clear the disabled state if user presses back button (not critical but nice)
        window.addEventListener('pageshow', function() {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
            }
        });
        
        // Additional robust fix to handle dynamic autofill from password managers that sometimes inject extra icons
        // Ensure that after any DOM changes, our single eye remains the only one
        function enforceSingleEye() {
            const container = document.querySelector('.password-container');
            if (!container) return;
            const eyes = container.querySelectorAll('.toggle-eye');
            if (eyes.length > 1) {
                for (let i = 1; i < eyes.length; i++) {
                    if (eyes[i] && eyes[i].parentNode) eyes[i].remove();
                }
            }
            // Also ensure the eye is visible and has correct event attached
            const primaryEye = container.querySelector('.toggle-eye');
            if (primaryEye && passwordInput) {
                // Re-attach if lost (safety)
                if (!primaryEye._listener) {
                    const newToggle = function(ev) {
                        ev.preventDefault();
                        const currentType = passwordInput.getAttribute('type');
                        const newType = currentType === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', newType);
                        if (newType === 'text') {
                            primaryEye.classList.remove('fa-eye');
                            primaryEye.classList.add('fa-eye-slash');
                        } else {
                            primaryEye.classList.remove('fa-eye-slash');
                            primaryEye.classList.add('fa-eye');
                        }
                    };
                    primaryEye.addEventListener('click', newToggle);
                    primaryEye._listener = newToggle;
                }
            }
        }
        
        // Run after a short delay to catch any post-render injections
        setTimeout(enforceSingleEye, 100);
        // Also observe if someone modifies the DOM dynamically (though not needed normally)
        const bodyObserver = new MutationObserver(function() {
            enforceSingleEye();
        });
        bodyObserver.observe(document.body, { childList: true, subtree: true });
        
        // Console log to confirm fix (for developers, can be removed in production)
        console.log('Password eye toggle fixed: single eye button, no duplication on typing or focus');
    </script>
</body>
</html>