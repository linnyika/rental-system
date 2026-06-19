<!DOCTYPE html>
<html>
<head>
    <title>Rental Property Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

<div class="container">
    <div class="row justify-content-center vh-100 align-items-center">

        <div class="col-md-5">
            <div class="card shadow">

                <div class="card-header text-center">
                    <h3>Rental Property Management System</h3>
                </div>

                <div class="card-body">

                    <form id="loginForm">

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text"
                                   id="phone"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   id="password"
                                   class="form-control"
                                   required>
                        </div>

                        <button class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                    <p id="message" class="mt-3 text-center"></p>

                </div>

            </div>
        </div>

    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const response = await fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            phone: document.getElementById('phone').value,
            password: document.getElementById('password').value
        })
    });

    const data = await response.json();

    if (response.ok) {

        localStorage.setItem('token', data.token);
        localStorage.setItem('role', data.user.role);

        if (data.user.role === 'landlord') {
            window.location.href = '/landlord/dashboard';
        }

        if (data.user.role === 'tenant') {
            window.location.href = '/tenant/dashboard';
        }

        if (data.user.role === 'caretaker') {
            window.location.href = '/caretaker/dashboard';
        }

    } else {
        document.getElementById('message').innerHTML =
            `<span class="text-danger">${data.message}</span>`;
    }
});
</script>

</body>
</html>