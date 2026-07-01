<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OfflineSync
{
	public function handle(Request $request, Closure $next)
	{
		if (!config('offlinesync.enabled', false)) {
			return $next($request);
		}

		$header = config('offlinesync.header', 'X-Offline-Sync');
		$isSyncRequest = (bool) $request->header($header, false);

		if (!$isSyncRequest) {
			return $next($request);
		}

		$maxPayloadKb = (int) config('offlinesync.max_payload_kb', 256);
		$contentLengthBytes = (int) $request->server('CONTENT_LENGTH', 0);
		if ($maxPayloadKb > 0 && $contentLengthBytes > ($maxPayloadKb * 1024)) {
			return response()->json([
				'success' => false,
				'message' => 'Sync payload exceeds allowed size.',
			], 413);
		}

		$request->attributes->set('offline_sync', [
			'enabled' => true,
			'requested_at' => now()->toIso8601String(),
		]);

		return $next($request);
	}
}
