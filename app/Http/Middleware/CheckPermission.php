<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        $perms = $user->permissions ?? [];
        if (is_string($perms)) {
            $perms = json_decode($perms, true) ?: [];
        }

        if (! in_array($permission, $perms)) {
            abort(403);
        }

        return $next($request);
    }
}
