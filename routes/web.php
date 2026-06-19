<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});
Route::view('/landlord/dashboard', 'landlord.dashboard');
Route::view(
    '/landlord/properties',
    'landlord.properties'
);
Route::get(
    '/landlord/units/{id}',
    function () {
        return view('landlord.units');
    }
);
Route::view(
    '/landlord/caretakers',
    'landlord.caretakers'
);
Route::view('/landlord/tenants', 'landlord.tenants');
Route::view('/landlord/tenants', 'landlord.tenants');