<?php

namespace App\Http\Middleware;

use Closure;

class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('X-API-TOKEN-ORGANIZATION');
        // Check against a token from DB or session etc.
        if ($token !== 'MagicWeb.org EOOD') {
            // Additional security for production
            abort(401, 'Auth token not found');
        }
        return $next($request);
    }
}
