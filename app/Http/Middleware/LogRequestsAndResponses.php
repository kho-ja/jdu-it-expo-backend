<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestsAndResponses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $requestCode = uniqid();
        $requestMethod = $request->method();
        $requestPath = $request->path();
        $data = $request->all();
        Log::info("$requestCode - REQUEST $requestMethod $requestPath", $data);

        // Handle the request and get the response
        $response = $next($request);
        $responseStatus = $response->status();
        $responseData = $response->content();
        Log::info("$requestCode - RESPONSE - $responseStatus $responseData");

        return $response;
    }

}
