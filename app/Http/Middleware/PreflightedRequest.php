<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class Headers
 * Http headers
 * @package App\Http\Middleware
 */
class PreflightedRequest
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Accept, Authorization, Content-Type, X-API-TOKEN-ORGANIZATION, X-API-CLIENT-APP-IDENTIFIER');
        $response->headers->set('Access-Control-Expose-Headers', 'CONTENT-DISPOSITION, X-MWEB-FNAME-DISPOSITION');
        return $response;
    }
}
