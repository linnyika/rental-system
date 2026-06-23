<!DOCTYPE html>
<html>
<head>
    <title>Units</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="container py-4">

<h2>Units</h2>

<div class="card p-3 mb-4">

    <h4>Add Unit</h4>

    <input
        type="text"
        id="unit_number"
        class="form-control mb-2"
        placeholder="Unit Number">

    <input
        type="number"
        id="rent_amount"
        class="form-control mb-2"
        placeholder="Rent Amount">

    <button
        class="btn btn-success"
        onclick="addUnit()">
        Save Unit
    </button>

</div>

<div id="unitList"></div>

<script>

const sessionToken = @json(session('api_token'));

if (sessionToken) {
    localStorage.setItem('token', sessionToken);
}

const token = localStorage.getItem('token');

const propertyId =
window.location.pathname.split('/').pop();

async function loadUnits() {

    const response =
    await fetch(`/api/properties/${propertyId}/units`, {

        headers: {
            Authorization: `Bearer ${token}`
        }
    });

    const data = await response.json();

    let html = '';

    data.units.forEach(unit => {

        html += `
        <div class="card mb-2">
            <div class="card-body">

                <strong>
                    Unit ${unit.unit_number}
                </strong>

                <br>

                Rent:
                KES ${unit.rent_amount}

                <br>

                Status:
                ${unit.is_occupied
                    ? 'Occupied'
                    : 'Available'}

            </div>
        </div>
        `;
    });

    document.getElementById('unitList')
        .innerHTML = html;
}

async function addUnit() {

    const response =
    await fetch(
        `/api/properties/${propertyId}/units`,
        {
            method:'POST',

            headers:{
                'Content-Type':'application/json',
                Authorization:`Bearer ${token}`
            },

            body:JSON.stringify({

                unit_number:
                    document.getElementById(
                        'unit_number'
                    ).value,

                rent_amount:
                    document.getElementById(
                        'rent_amount'
                    ).value
            })
        }
    );

    if(response.ok){

        loadUnits();

        document.getElementById(
            'unit_number'
        ).value='';

        document.getElementById(
            'rent_amount'
        ).value='';
    }
}

loadUnits();

</script>

</body>
</html>
