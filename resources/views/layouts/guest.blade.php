<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" data-theme="light">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $image_logo->company_name ?? 'Law Office' }} | {{ __('login') ?? 'Login' }}</title>
    @if(isset($image_logo) && $image_logo->favicon_img)
    <link rel="shortcut icon" href="{{URL::asset(config('constants.FAVICON_FOLDER_PATH') .'/'. $image_logo->favicon_img)}}" >
    @endif
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            baseUrl: '{{ url('/') }}'
        };
    </script>
    
    <style media="screen">
        :root {
            --primary-color: #0badf8;
            --primary-dark: #0892d0;
            --text-color: #ffffff;
            --bg-color: #080710;
            --form-bg: rgba(0,0,0,0.3);
            --border-color: rgba(255,255,255,0.1);
            --shadow-color: rgba(8,7,16,0.6);
            --input-bg: rgba(255,255,255,0.07);
            --input-focus-color: #0badf8;
            --input-shadow: rgba(26, 143, 180, 0.3);
        }
        
        [data-theme="dark"] {
            --primary-color: #0badf8;
            --primary-dark: #0892d0;
            --text-color: #ffffff;
            --bg-color: #080710;
            --form-bg: rgba(0,0,0,0.3);
            --border-color: rgba(255,255,255,0.1);
            --shadow-color: rgba(8,7,16,0.6);
            --input-bg: rgba(255,255,255,0.07);
            --input-focus-color: #0badf8;
            --input-shadow: rgba(26, 143, 180, 0.3);
        }
        
        [data-theme="light"] {
            --primary-color: #0badf8;
            --primary-dark: #0892d0;
            --text-color: #333;
            --bg-color: #e0f7fa;
            --form-bg: rgba(255,255,255,0.9);
            --border-color: rgba(0,0,0,0.1);
            --shadow-color: rgba(8,7,16,0.2);
            --input-bg: rgba(0,0,0,0.05);
            --input-focus-color: #0badf8;
            --input-shadow: rgba(26, 143, 180, 0.3);
        }
        
        *,
        *:before,
        *:after{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        
        body{
            background-color: var(--bg-color);
            font-family: 'Poppins', sans-serif;
        }
        
        .background{
            width: 430px;
            height: 520px;
            position: absolute;
            transform: translate(-50%,-50%);
            left: 50%;
            top: 50%;
            z-index: -1;
        }
        
        .background .shape{
            height: 200px;
            width: 200px;
            position: absolute;
            border-radius: 50%;
        }
        
        .shape:first-child{
            background: linear-gradient(
rgb(11, 173, 248),
                #80deea
            );
            left: -80px;
            top: -80px;
        }
        
        .shape:last-child{
            background: linear-gradient(
                to right,
rgb(0, 138, 5),
rgb(2, 236, 92)
            );
            right: -30px;
            bottom: -80px;
        }
        
        .login-form{
            height: 700px;
            width: 400px;
            background-color: var(--form-bg);
            position: absolute;
            transform: translate(-50%,-50%);
            top: 50%;
            left: 50%;
            border-radius: 5px;
            backdrop-filter: blur(10px);
            border: 2px solid var(--border-color);
            box-shadow: 0 0 40px var(--shadow-color);
            padding: 15px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .login-form *{
            color: var(--text-color);
            letter-spacing: 0.5px;
            outline: none;
            border: none;
        }
        
        .logo-center {
            display: block;
            margin: 0 auto 25px auto;
            width: 170px;
            height: 170px;
            object-fit: cover;
            object-position: center;
            border-radius: 50%;
            border: 10px solid rgba(31, 161, 48, 0.59);
            box-shadow: 0 0 20px rgba(19, 187, 230, 0.94);
            padding: 0;
            background: none;
        }
        
        .login-form h3{
            font-size: 25px;
            font-weight: 400;
            line-height: 35px;
            text-align: center;
            margin-bottom: 15px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #00bcd4;
            text-shadow: 0 0 10px rgba(0, 188, 212, 0.3);
        }
        
        .login-form label{
            display: block;
            margin-top: 10px;
            font-size: 16px;
            font-weight: 500;
            letter-spacing: 0.5px;
            color: rgba(255,255,255,0.9);
        }
        
        .login-form input{
            display: block;
            height: 50px;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            padding: 0 15px;
            margin-top: 8px;
            font-size: 15px;
            font-weight: 300;
            color: #ffffff;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .login-form input:focus {
            background-color: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(0, 188, 212, 0.4);
            box-shadow: 0 0 8px rgba(0, 188, 212, 0.2);
            outline: none;
        }
        
        .login-form input::placeholder{
            color: rgba(255, 255, 255, 0.4);
        }
        
        .login-form button{
            margin-top: 40px;
            width: 100%;
            background-color: rgba(0, 188, 212, 0.3);
            color: #ffffff;
            padding: 15px 0;
            font-size: 18px;
            font-weight: 600;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .login-form button:hover {
            background-color: rgba(0, 188, 212, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        
        .login-form .remember-me {
            display: flex;
            align-items: center;
            margin-top: 25px;
        }
        
        .login-form .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
            background-color: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            backdrop-filter: blur(10px);
        }
        
        .login-form .remember-me label {
            margin-top: 0;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .login-form .forgot-password {
            text-align: right;
            margin-top: 15px;
        }
        
        .login-form .forgot-password a {
            color: rgba(0, 188, 212, 0.8);
            text-decoration: none;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .login-form .forgot-password a:hover {
            color: rgba(0, 188, 212, 1);
            text-shadow: 0 0 5px rgba(0, 188, 212, 0.3);
        }
        
        .form-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .form-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin: 10px 0;
        }
        
        .login-form button{
            margin-top: 25px;
        }
        
        .login-form .footer-text {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .login-form .footer-text p {
            color: rgba(255,255,255,0.8);
            margin-bottom: 5px;
        }
        
        .login-form .footer-text p:last-child {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
        }
        
        .alert {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: rgba(220, 53, 69, 0.3);
            color: #fff;
        }
        
        /* Language and Theme Toggle Styles */
        .toggle-container {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 15px;
            z-index: 1000;
        }
        
        .language-toggle {
            background-color: var(--form-bg);
            border-radius: 4px;
            padding: 5px 10px;
            box-shadow: 0 2px 5px var(--shadow-color);
            display: flex;
            align-items: center;
        }
        
        .language-toggle a {
            color: var(--text-color);
            text-decoration: none;
            padding: 3px 8px;
            border-radius: 3px;
            transition: background-color 0.3s;
        }
        
        .language-toggle a:hover {
            background-color: rgba(11, 173, 248, 0.1);
        }
        
        .language-toggle i {
            margin-right: 5px;
            color: var(--primary-color);
        }
        
        /* Theme Toggle Button */
        .theme-toggle-wrapper {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
    
    <script>
        // Force redirect to login page when home button is clicked - Firefox compatible
        document.getElementById('homeButton').addEventListener('click', function(e) {
            e.preventDefault();
            // Clear any existing session data
            if (window.sessionStorage) {
                sessionStorage.clear();
            }
            // Force a hard redirect
            window.location.replace('/admin/login?t=' + new Date().getTime());
            // Force reload after a short delay if still on the same page
            setTimeout(function() {
                if (window.location.pathname !== '/admin/login') {
                    window.location.href = '/admin/login?t=' + new Date().getTime();
                }
            }, 100);
        });
    </script>
    
   
       
    
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
      
      <form class="login-form" method="POST" action="{{ route('login') }}">
          {{ csrf_field() }}
          
          <div class="form-header">
              <img src="{{ asset('images/company-logo.png') }}" class="logo-center" alt="Company Logo">
              <h4>{{ __('Legal Case Management System') ?? 'Legal Case Management System' }}</h4>
              
              @if ($errors->has('email') || $errors->has('password'))
              <div class="alert">
                  @if ($errors->has('email'))
                      {{ $errors->first('email') }}
                  @endif
                  @if ($errors->has('password'))
                      {{ $errors->first('password') }}
                  @endif
              </div>
              @endif
          </div>
          
          <div class="form-content">
              <label for="email">{{ __('email') ?? 'Email' }}</label>
              <input type="email" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required autofocus>
              
              <label for="password">{{ __('password') ?? 'Password' }}</label>
              <input type="password" id="password" name="password" placeholder="Enter your password" required>
              
              <div class="form-actions">
                  <div class="forgot-password">
                      <a href="{{ url('/admin/password/reset') }}">Forgot Password?</a>
                  </div>
              </div>
              
              <button type="submit">{{ __('login') ?? 'Log In' }}</button>
          </div>
          
          <div class="footer-text">
              @if(isset($image_logo) && $image_logo->company_name)
              <p>{{ $image_logo->company_name }}</p>
              @else
              <p>Law Office</p>
              @endif
              <p>©{{date('Y')}} All Rights Reserved</p>
          </div>
      </form>
    </div>
    <!-- jQuery -->
    <script src="{{asset('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            "use strict";
            $(".fill-login").click(function () {
                $("#email").val($(this).data("email"));
                $("#password").val($(this).data("password"));
            });
        });
    </script>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Theme toggle functionality
        const TOGGLE_BUTTON = document.querySelector(".theme-toggle");
        const THEME_KEY = 'lcms-theme-preference';
        const HTML = document.documentElement;
        
        // Function to set theme
        function setTheme(isDark) {
            // Update toggle button state
            TOGGLE_BUTTON.setAttribute("aria-pressed", isDark);
            
            // Set dark mode custom property for animation
            HTML.style.setProperty('--dark', isDark ? 1 : 0);
            
            // Update document theme
            HTML.setAttribute("data-theme", isDark ? "dark" : "light");
            
            // Add transition class for smooth theme change
            document.body.classList.add('theme-transition');
            
            // Apply styles to form elements based on theme
            applyThemeStyles(isDark);
            
            // Store preference
            localStorage.setItem(THEME_KEY, isDark ? "dark" : "light");
            
            // Remove transition class after animation completes
            setTimeout(() => {
                document.body.classList.remove('theme-transition');
            }, 500);
        }
        
        // Apply theme-specific styles to form elements
        function applyThemeStyles(isDark) {
            const loginForm = document.querySelector('.login-form');
            const formElements = loginForm.querySelectorAll('input, button, label, p, h3, a');
            
            if (isDark) {
                loginForm.style.backgroundColor = 'rgba(0,0,0,0.5)';
                loginForm.style.borderColor = 'rgba(255,255,255,0.1)';
                
                formElements.forEach(el => {
                    if (el.tagName === 'INPUT') {
                        el.style.backgroundColor = 'rgba(0,0,0,0.3)';
                        el.style.color = '#ffffff';
                        el.style.borderColor = 'rgba(255,255,255,0.1)';
                    } else if (el.tagName === 'LABEL' || el.tagName === 'P') {
                        el.style.color = 'rgba(255,255,255,0.9)';
                    }
                });
            } else {
                loginForm.style.backgroundColor = 'rgba(255,255,255,0.9)';
                loginForm.style.borderColor = 'rgba(0,0,0,0.1)';
                
                formElements.forEach(el => {
                    if (el.tagName === 'INPUT') {
                        el.style.backgroundColor = 'rgba(0,0,0,0.1)';
                        el.style.color = '#333333';
                        el.style.borderColor = 'rgba(0,0,0,0.1)';
                    } else if (el.tagName === 'LABEL' || el.tagName === 'P') {
                        el.style.color = 'rgba(0,0,0,0.9)';
                    }
                });
            }
        }
        
        // Initialize theme based on stored preference
        function initializeTheme() {
            const storedTheme = localStorage.getItem(THEME_KEY);
            if (storedTheme) {
                setTheme(storedTheme === "dark");
            } else {
                // Default to light theme
                setTheme(false);
            }
        }
        
        // Initialize theme
        initializeTheme();
        
        // Toggle theme when button is clicked
        TOGGLE_BUTTON.addEventListener("click", function() {
            const isCurrentlyDark = TOGGLE_BUTTON.getAttribute("aria-pressed") === "true";
            setTheme(!isCurrentlyDark);
        });
    });
    </script>
</body>
</html>







