<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class Logging
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        // Log request and response to storage/logs/laravel.log
        Log::debug(
            "{$request->path()}[{$request->method()}] returns [{$response->status()}]HTTP <-"
            . ($request->user() ? $request->user()->getFullName() : 'no-user')
        );
    }
}
