<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $request->headers->set('Access-Control-Allow-Origin', '*');
        $request->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $request->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        return $next($request);
    }
}
