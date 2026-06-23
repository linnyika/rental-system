<!DOCTYPE html>
<html>
<head>
    <title>Landlord Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="container py-4">

    <h2 class="mb-4">Landlord Dashboard</h2>

    <div class="row">

        <div class="col-md-3 mb-3">
            <a href="{{ route('landlord.properties.index') }}" class="btn btn-primary w-100">
                Manage Properties
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <a href="{{ route('landlord.caretakers.create') }}" class="btn btn-success w-100">
                Register Caretaker
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <a href="{{ route('landlord.tenants.create') }}" class="btn btn-warning w-100">
                Register Tenant
            </a>
        </div>

        <div class="col-md-3 mb-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-danger w-100" type="submit">Logout</button>
            </form>
        </div>

    </div>

    <hr>

    <div class="card">
        <div class="card-body">
            <h4>Welcome</h4>
            <p>
                Use the buttons above to manage properties, units,
                caretakers and tenants.
            </p>
        </div>
    </div>

    <script>
        const token = @json(session('api_token'));

        if (token) {
            localStorage.setItem('token', token);
            localStorage.setItem('role', 'landlord');
        }
    </script>

</body>
</html>
