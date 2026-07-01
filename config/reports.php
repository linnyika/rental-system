<?php

return [
	'enabled' => (bool) env('REPORTS_ENABLED', true),
	'timezone' => env('REPORTS_TIMEZONE', env('APP_TIMEZONE', 'UTC')),
	'currency' => env('REPORTS_CURRENCY', 'KES'),
	'activity_log' => [
		'enabled' => (bool) env('REPORTS_ACTIVITY_LOG_ENABLED', true),
		'retention_days' => (int) env('REPORTS_ACTIVITY_LOG_RETENTION_DAYS', 180),
	],
	'financial' => [
		'default_range_months' => (int) env('REPORTS_DEFAULT_RANGE_MONTHS', 6),
	],
];
