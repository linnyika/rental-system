<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rental Property Management System - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/yeti/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #18babd 20%, #1f3648 70%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px #1f364833;
            );
            max-width: 420px;
            width: 100%;
            animation: fadeInUp 0.6s ease;
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

        .login-container h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
        }

        .login-container .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .login-container .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e1e5eb;
            transition: all 0.3s ease;
        }

        .login-container .form-control:focus {
            border-color: #10918c;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .login-container .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .login-container .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-container .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .login-container .btn-primary:active {
            transform: translateY(0);
        }

        .login-container .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .login-container .btn-primary .spinner {
            display: none;
            width: 20px;
            height: 20px;
            margin: 0 auto;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s ease infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .alert {
            border-radius: 10px;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e1e5eb;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            background: none;
            border: none;
            padding: 0;
        }

        .password-toggle:hover {
            color: #333;
        }

        .form-group {
            position: relative;
        }

        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .error-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Toast notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast-custom {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            margin-bottom: 10px;
            min-width: 300px;
            animation: slideInRight 0.5s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .toast-custom.success {
            border-left: 4px solid #28a745;
        }

        .toast-custom.error {
            border-left: 4px solid #dc3545;
        }

        .toast-custom.info {
            border-left: 4px solid #17a2b8;
        }

        .toast-custom .toast-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .toast-custom .toast-message {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="login-container">
        <h2>Rental System</h2>
        <p class="subtitle">Login to Your Account</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="mb-3 form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
                <div class="invalid-feedback" id="emailError">
                    @error('email')
                        {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="mb-3 form-group">
                <label for="password" class="form-label">Password</label>
                <div style="position: relative;">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" placeholder="Enter your password" required minlength="8">
                    <button type="button" class="password-toggle" id="togglePassword"
                        aria-label="Toggle password visibility">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="passwordError">
                    @error('password')
                        {{ $message }}
                    @enderror

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-primary" id="loginButton">
                        <span id="buttonText">Login</span>
                        <span class="spinner" id="buttonSpinner"></span>
                    </button>
        </form>



        <!-- Bootstrap JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Bootstrap Icons (for password toggle) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</body>

</html>
