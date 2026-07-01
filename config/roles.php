<?php

return [
	'default' => env('DEFAULT_USER_ROLE', 'tenant'),
	'available' => [
		'admin',
		'landlord',
		'caretaker',
		'tenant',
	],
	'public_registration' => [
		'landlord',
	],
	'dashboard_routes' => [
		'admin' => 'admin.dashboard',
		'landlord' => 'landlord.dashboard',
		'caretaker' => 'caretaker.dashboard',
		'tenant' => 'tenant.dashboard',
	],
];
