<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مصنع المروة للكرتون</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #1a5276;
            --primary-dark: #0e3a5c;
            --accent: #d4a017;
            --accent-light: #f0c75e;
            --surface: rgba(255,255,255,0.08);
            --glass: rgba(255,255,255,0.12);
            --text-primary: #ffffff;
            --text-muted: rgba(255,255,255,0.7);
            --shadow: 0 25px 60px rgba(0,0,0,0.4);
            --radius: 20px;
        }

        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0c2340 0%, #1a5276 40%, #0e3a5c 70%, #0a1628 100%);
            overflow: hidden;
            position: relative;
        }

        /* ====== Animated Background Shapes ====== */
        .bg-shapes {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .bg-shapes span {
            position: absolute;
            display: block;
            border: 2px solid rgba(212, 160, 23, 0.12);
            border-radius: 8px;
            animation: floatBox 18s infinite linear;
            opacity: 0;
        }
        .bg-shapes span:nth-child(1) { width: 60px; height: 60px; top: 10%; left: 15%; animation-delay: 0s; animation-duration: 20s; }
        .bg-shapes span:nth-child(2) { width: 90px; height: 90px; top: 60%; left: 80%; animation-delay: 3s; animation-duration: 25s; }
        .bg-shapes span:nth-child(3) { width: 40px; height: 40px; top: 30%; left: 60%; animation-delay: 6s; animation-duration: 22s; }
        .bg-shapes span:nth-child(4) { width: 120px; height: 120px; top: 75%; left: 20%; animation-delay: 2s; animation-duration: 28s; }
        .bg-shapes span:nth-child(5) { width: 50px; height: 50px; top: 15%; left: 85%; animation-delay: 5s; animation-duration: 18s; }
        .bg-shapes span:nth-child(6) { width: 70px; height: 70px; top: 85%; left: 50%; animation-delay: 8s; animation-duration: 23s; }

        @keyframes floatBox {
            0%   { transform: translateY(0) rotate(0deg); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-120vh) rotate(720deg); opacity: 0; }
        }

        /* ====== Main Card ====== */
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .login-card {
            background: linear-gradient(165deg, rgba(26,82,118,0.6) 0%, rgba(14,58,92,0.8) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 160, 23, 0.2);
            border-radius: var(--radius);
            padding: 48px 40px;
            box-shadow: var(--shadow), 0 0 80px rgba(212, 160, 23, 0.08);
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes cardEntrance {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ====== Logo / Brand ====== */
        .brand {
            text-align: center;
            margin-bottom: 36px;
        }

        .brand-icon {
            width: 88px;
            height: 88px;
            margin: 0 auto 18px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(212, 160, 23, 0.35);
            animation: iconPulse 3s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 8px 32px rgba(212, 160, 23, 0.35); }
            50% { box-shadow: 0 8px 48px rgba(212, 160, 23, 0.55); }
        }

        .brand-icon i {
            font-size: 42px;
            color: var(--primary-dark);
        }

        .brand h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .brand p {
            font-size: 14px;
            color: var(--accent-light);
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* ====== Divider ====== */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(212, 160, 23, 0.3), transparent);
            margin-bottom: 32px;
        }

        /* ====== Form ====== */
        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 8px;
            transition: color 0.3s;
        }

        .form-group:focus-within label {
            color: var(--accent-light);
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.35);
            font-size: 18px;
            transition: color 0.3s;
            pointer-events: none;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 48px 14px 16px;
            background: var(--surface);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'Cairo', sans-serif;
            font-size: 15px;
            font-weight: 600;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-wrapper input::placeholder {
            color: rgba(255,255,255,0.3);
            font-weight: 400;
        }

        .input-wrapper input:focus {
            border-color: var(--accent);
            background: rgba(255,255,255,0.12);
            box-shadow: 0 0 0 4px rgba(212, 160, 23, 0.12);
        }

        .input-wrapper input:focus + i,
        .input-wrapper input:focus ~ i {
            color: var(--accent-light);
        }

        .toggle-password {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,255,255,0.35);
            cursor: pointer;
            font-size: 18px;
            padding: 4px;
            transition: color 0.3s;
            pointer-events: all;
        }

        .toggle-password:hover {
            color: var(--accent-light);
        }

        /* ====== Error Message ====== */
        .error-msg {
            display: none;
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            color: #ff8a8a;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            animation: shakeError 0.5s ease;
        }

        .error-msg.show {
            display: block;
        }

        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        /* ====== Submit Button ====== */
        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--accent) 0%, #c4941a 100%);
            border: none;
            border-radius: 12px;
            color: var(--primary-dark);
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transition: left 0.6s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(212, 160, 23, 0.45);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(14,58,92,0.3);
            border-top-color: var(--primary-dark);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 0 auto;
        }

        .btn-login.loading .btn-text { display: none; }
        .btn-login.loading .spinner { display: inline-block; }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ====== Footer ====== */
        .card-footer {
            text-align: center;
            margin-top: 28px;
            color: rgba(255,255,255,0.35);
            font-size: 12px;
            font-weight: 600;
        }

        .card-footer i {
            color: var(--accent);
            margin: 0 4px;
        }

        /* ====== Responsive ====== */
        @media (max-width: 520px) {
            .login-card {
                padding: 36px 24px;
            }
            .brand h1 {
                font-size: 23px;
            }
            .brand-icon {
                width: 72px;
                height: 72px;
            }
            .brand-icon i {
                font-size: 34px;
            }
        }
    </style>
</head>
<body>

    <!-- Animated Background Shapes (Carton Boxes) -->
    <div class="bg-shapes">
        <span></span><span></span><span></span>
        <span></span><span></span><span></span>
    </div>

    <div class="login-wrapper">
        <div class="login-card">
            <!-- Brand -->
            <div class="brand">
                <div class="brand-icon">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <h1>مصنع المروة للكرتون</h1>
                <p>ElMarwa Carton Factory</p>
            </div>

            <div class="divider"></div>

            <!-- Error Message -->
            <div class="error-msg" id="errorMsg">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span id="errorText">اسم المستخدم أو كلمة المرور غير صحيحة</span>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">
                        <i class="bi bi-person-fill"></i>
                        اسم المستخدم / البريد الإلكتروني
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            id="email"
                            name="email"
                            placeholder="أدخل اسم المستخدم..."
                            autocomplete="username"
                            required
                            autofocus
                            value="{{ old('email') }}"
                        >
                        <i class="bi bi-person"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="bi bi-lock-fill"></i>
                        كلمة المرور
                    </label>
                    <div class="input-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="أدخل كلمة المرور..."
                            autocomplete="current-password"
                            required
                        >
                        <i class="bi bi-lock"></i>
                        <button type="button" class="toggle-password" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <span class="btn-text"><i class="bi bi-box-arrow-in-left"></i> تسجيل الدخول</span>
                    <span class="spinner"></span>
                </button>
            </form>

            <div class="card-footer">
                <i class="bi bi-shield-lock-fill"></i>
                نظام إدارة مصنع المروة للكرتون &copy; {{ date('Y') }}
            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            // Toggle Password Visibility
            var toggleBtn = document.getElementById('togglePassword');
            var passwordInput = document.getElementById('password');

            if (toggleBtn && passwordInput) {
                toggleBtn.addEventListener('click', function() {
                    var icon = this.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            }

            // Form Submit - Loading State
            var form = document.getElementById('loginForm');
            var btnLogin = document.getElementById('btnLogin');

            if (form && btnLogin) {
                form.addEventListener('submit', function() {
                    btnLogin.classList.add('loading');
                    btnLogin.disabled = true;
                });
            }

            // Show errors from Laravel validation
            @if($errors->any())
                var errorMsg = document.getElementById('errorMsg');
                var errorText = document.getElementById('errorText');
                if (errorMsg && errorText) {
                    errorText.textContent = '{!! $errors->first() !!}';
                    errorMsg.classList.add('show');
                }
            @endif
        })();
    </script>
</body>
</html>
