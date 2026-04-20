<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        if (! $request->user()?->can($permission)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
