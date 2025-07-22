<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Legal Case Management') }} - Login</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-green: #3ca44c;
            --dark-green: #1e3a2e;
            --accent-blue: #2563eb;
            --light-green: #f0f9f1;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 50%, var(--accent-blue) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), var(--accent-blue));
        }
        
        [data-theme="dark"] .login-card {
            background: #1f2937;
            color: white;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }
        
        .company-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .system-title {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        [data-theme="dark"] .system-title {
            color: white;
        }
        
        .system-subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 400;
        }
        
        [data-theme="dark"] .system-subtitle {
            color: #9ca3af;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }
        
        [data-theme="dark"] .form-label {
            color: #d1d5db;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(60, 164, 76, 0.1);
        }
        
        [data-theme="dark"] .form-input {
            background: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: color 0.3s ease;
        }
        
        .password-toggle:hover {
            color: var(--primary-green);
        }
        
        [data-theme="dark"] .password-toggle {
            color: #9ca3af;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            accent-color: var(--primary-green);
        }
        
        .remember-me label {
            font-size: 0.875rem;
            color: #6b7280;
            cursor: pointer;
        }
        
        [data-theme="dark"] .remember-me label {
            color: #9ca3af;
        }
        
        .forgot-password {
            font-size: 0.875rem;
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-password:hover {
            color: var(--dark-green);
        }
        
        .login-button {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-green), var(--accent-blue));
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(60, 164, 76, 0.3);
        }
        
        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        [data-theme="dark"] .error-message {
            background: #1f1917;
            border-color: #451a03;
            color: #f87171;
        }
        
        .success-message {
            background: var(--light-green);
            border: 1px solid rgba(60, 164, 76, 0.3);
            color: var(--dark-green);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }
        
        [data-theme="dark"] .success-message {
            background: #0f1f13;
            color: #4ade80;
        }
        
        @media (max-width: 640px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                max-width: 100%;
            }
            
            .login-header {
                padding: 2rem 1.5rem 1.5rem;
            }
            
            .login-form {
                padding: 2rem 1.5rem;
            }
            
            .system-title {
                font-size: 1.5rem;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Header Section -->
        <div class="login-header">
            <div class="logo-container">
                <img src="{{ asset('images/company-logo.png') }}" alt="Legal Organization Logo" class="company-logo">
            </div>
            <h1 class="system-title">Legal Case Management</h1>
            <p class="system-subtitle">Legal Organization Legal Department</p>
        </div>
                <!-- Session Status -->
                @if (session('status'))
                    <div class="success-message">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('status') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
        <!-- Email Address -->
        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <div class="input-wrapper">
                <input id="email" 
                       class="form-input" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus 
                       autocomplete="username"
                       placeholder="Enter your email address">
            </div>
            @error('email')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>
        
        <!-- Password -->
        <div class="form-group">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <div class="input-wrapper">
                <input id="password" 
                       class="form-input" 
                       type="password" 
                       name="password" 
                       required 
                       autocomplete="current-password"
                       placeholder="Enter your password">
                <button type="button" class="password-toggle" onclick="togglePassword()">
                    <i class="fas fa-eye" id="password-icon"></i>
                </button>
            </div>
            @error('password')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input id="remember_me" type="checkbox" name="remember">
                            <label for="remember_me">{{ __('Remember me') }}</label>
                        </div>
                        
                        @if (Route::has('password.request'))
                            <a class="forgot-password" href="{{ route('password.request') }}">
                                <i class="fas fa-question-circle mr-1"></i>
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>
                    
        <!-- Login Button -->
        <button type="submit" class="login-button">
            {{ __('Sign In') }}
        </button>
    </form>
    
    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        });
        
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        
        // Add entrance animation
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.login-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</div>
</body>
</html>






