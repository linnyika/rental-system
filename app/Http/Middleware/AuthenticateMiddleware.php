<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateMiddleware
{
	public function handle(Request $request, Closure $next, ...$guards)
	{
		$guards = empty($guards) ? [null] : $guards;

		foreach ($guards as $guard) {
			if (Auth::guard($guard)->check()) {
				return $next($request);
			}
		}

		if ($request->expectsJson() || $request->is('api/*')) {
			return response()->json([
				'success' => false,
				'message' => 'Unauthenticated.',
			], 401);
		}

		return redirect()->route('login');
	}
}
