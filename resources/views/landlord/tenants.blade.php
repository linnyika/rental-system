<!DOCTYPE html>
<html>
<head>
    <title>Register Tenant</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="container py-4">

<h2>Register Tenant</h2>

<div class="card p-3">

    <input id="name" class="form-control mb-2" placeholder="Tenant Name">

    <input id="phone" class="form-control mb-2" placeholder="Phone">

    <input id="email" class="form-control mb-2" placeholder="Email">

    <input id="password" type="password"
           class="form-control mb-2"
           placeholder="Password">

    <input id="password_confirmation"
           type="password"
           class="form-control mb-2"
           placeholder="Confirm Password">

    <input id="unit_id"
           type="number"
           class="form-control mb-2"
           placeholder="Unit ID">

    <input id="start_date"
           type="date"
           class="form-control mb-2">

    <button class="btn btn-success"
            onclick="registerTenant()">
        Register Tenant
    </button>

</div>

<p id="message" class="mt-3"></p>

<script>

const token = localStorage.getItem('token');

async function registerTenant(){

    const response = await fetch('/api/tenants', {

        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
        },

        body: JSON.stringify({

            name: document.getElementById('name').value,
            phone: document.getElementById('phone').value,
            email: document.getElementById('email').value,

            password:
                document.getElementById('password').value,

            password_confirmation:
                document.getElementById('password_confirmation').value,

            unit_id:
                document.getElementById('unit_id').value,

            start_date:
                document.getElementById('start_date').value
        })
    });

    const data = await response.json();

    document.getElementById('message')
        .innerText = data.message;
}

</script>

</body>
</html>