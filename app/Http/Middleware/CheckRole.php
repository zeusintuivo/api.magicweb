<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // No user at all
        if (!$request->user()) {
            return response()->json(trans('auth.unauthenticated'), 401);
        }

        // Does not have guarded role
        if (!$request->user()->hasRole($role)) {
            return response()->json(trans('auth.access_not_granted'), 401);
        }

        return $next($request);
    }
}
