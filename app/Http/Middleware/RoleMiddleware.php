<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->with('error', 'Please login to continue.');
        }

        // Check if user has any of the allowed roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // User doesn't have the required role
        abort(403, 'Unauthorized access. You do not have the required permissions.');
    }
}
