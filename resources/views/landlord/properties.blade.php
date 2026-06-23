<!DOCTYPE html>
<html>
<head>
    <title>Properties</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="container py-4">

<h2>Properties</h2>

<div class="card p-3 mb-4">

    <h4>Add Property</h4>

    <input type="text"
           id="name"
           class="form-control mb-2"
           placeholder="Property Name">

    <input type="text"
           id="address"
           class="form-control mb-2"
           placeholder="Address">

    <button class="btn btn-success"
            onclick="addProperty()">
        Save Property
    </button>

</div>

<div id="propertyList"></div>

<script>

const sessionToken = @json(session('api_token'));

if (sessionToken) {
    localStorage.setItem('token', sessionToken);
}

const token = localStorage.getItem('token');

async function loadProperties() {

    const response = await fetch('/api/properties', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    });

    const data = await response.json();

    let html = '';

    data.properties.forEach(property => {

        html += `
        <div class="card mb-2">
            <div class="card-body">

                <h5>${property.name}</h5>

                <p>${property.address ?? ''}</p>

                <a href="/landlord/units/${property.id}"
                   class="btn btn-primary">
                   View Units
                </a>

            </div>
        </div>
        `;
    });

    document.getElementById('propertyList').innerHTML = html;
}

async function addProperty() {

    const response = await fetch('/api/properties', {

        method: 'POST',

        headers: {
            'Content-Type': 'application/json',
            Authorization: `Bearer ${token}`
        },

        body: JSON.stringify({
            name: document.getElementById('name').value,
            address: document.getElementById('address').value
        })
    });

    if(response.ok){
        loadProperties();

        document.getElementById('name').value='';
        document.getElementById('address').value='';
    }
}

loadProperties();

</script>

</body>
</html>
