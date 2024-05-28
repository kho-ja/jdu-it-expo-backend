<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $acceptLanguage = $request->header('Accept-Language');

        if ($acceptLanguage) {
            if (in_array($acceptLanguage, config('app.supported_languages'))) {
                app()->setLocale($acceptLanguage);
                return $next($request);
            }
        }
        return $next($request);
    }
}
