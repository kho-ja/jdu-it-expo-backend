<?php

namespace App\Http\Middleware;

use Closure;

class Role
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if ($user && in_array($user->role->name, $roles)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
