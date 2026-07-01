<?php

return [
	'enabled' => (bool) env('OFFLINE_SYNC_ENABLED', false),
	'header' => env('OFFLINE_SYNC_HEADER', 'X-Offline-Sync'),
	'max_payload_kb' => (int) env('OFFLINE_SYNC_MAX_PAYLOAD_KB', 256),
	'queue_connection' => env('OFFLINE_SYNC_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'database')),
	'retry_attempts' => (int) env('OFFLINE_SYNC_RETRY_ATTEMPTS', 3),
];
