<?php

namespace App\Http\Middleware;

use Closure;
use function app;

class SetLocale
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
        app()->setLocale($request['locale']);
        return $next($request);
    }
}
