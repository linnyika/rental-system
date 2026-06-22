<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental System - Register as Landlord</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 100%;
        }

        .register-container h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
            text-align: center;
        }

        .register-container .subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }

        .register-container .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e1e5eb;
        }

        .register-container .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .register-container .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            transition: transform 0.3s;
        }

        .register-container .btn-primary:hover {
            transform: translateY(-2px);
        }

        .register-container .btn-secondary {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
        }

        .alert {
            border-radius: 10px;
        }

        .role-badge {
            background: #e7f3ff;
            color: #667eea;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 20px;
            display: inline-block;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>🏠 Become a Landlord</h2>
        <p class="subtitle">Register your property management account</p>

        <div class="role-badge">
            <strong>👤 Landlord Registration</strong>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" value="{{ old('name') }}" placeholder="Enter your full name" required autofocus>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                    name="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                    name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    name="password" placeholder="Create a strong password (min 8 characters)" required>
                <small class="text-muted">Password must be at least 8 characters long.</small>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="Confirm your password" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                <label class="form-check-label" for="terms">
                    I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Register as Landlord</button>

            <div class="text-center mt-3">
                <p class="mb-0">Already have an account? <a href="{{ route('login') }}">Login here</a></p>
            </div>

            <hr class="my-3">

            <div class="text-center">
                <p class="text-muted small mb-0">
                    <strong>Note:</strong> Only Landlords can register themselves.
                    Tenants and Caretakers are registered by Landlords.
                </p>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
