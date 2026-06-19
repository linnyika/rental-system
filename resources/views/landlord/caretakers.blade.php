<!DOCTYPE html>
<html>
<head>
    <title>Register Caretaker</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="container py-4">

<h2>Register Caretaker</h2>

<div class="card p-3">

    <input id="name" class="form-control mb-2" placeholder="Name">

    <input id="phone" class="form-control mb-2" placeholder="Phone">

    <input id="email" class="form-control mb-2" placeholder="Email">

    <input id="password" type="password"
           class="form-control mb-2"
           placeholder="Password">

    <input id="password_confirmation"
           type="password"
           class="form-control mb-2"
           placeholder="Confirm Password">

    <button class="btn btn-success"
            onclick="registerCaretaker()">
        Register
    </button>

</div>

<p id="message"></p>

<script>

const token = localStorage.getItem('token');

async function registerCaretaker(){

    const response =
    await fetch('/api/caretakers',{

        method:'POST',

        headers:{
            'Content-Type':'application/json',
            Authorization:`Bearer ${token}`
        },

        body:JSON.stringify({

            name:document.getElementById('name').value,

            phone:document.getElementById('phone').value,

            email:document.getElementById('email').value,

            password:document.getElementById('password').value,

            password_confirmation:
            document.getElementById(
                'password_confirmation'
            ).value
        })
    });

    const data = await response.json();

    document.getElementById('message')
        .innerText = data.message;
}

</script>

</body>
</html>