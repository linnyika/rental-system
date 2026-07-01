<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LogActivityMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		$response = $next($request);

		if (!config('reports.activity_log.enabled', true)) {
			return $response;
		}

		try {
			$user = $request->user();

			DB::table('activity_logs')->insert([
				'user_id' => $user?->id,
				'action' => strtolower($request->method()) . ' ' . $request->path(),
				'entity_type' => null,
				'entity_id' => null,
				'details' => json_encode([
					'status' => method_exists($response, 'status') ? $response->status() : null,
					'query' => $request->query(),
				]),
				'ip_address' => $request->ip(),
				'created_at' => now(),
			]);
		} catch (Throwable $e) {
			// Never block the request lifecycle due to activity logging failure.
		}

		return $response;
	}
}
