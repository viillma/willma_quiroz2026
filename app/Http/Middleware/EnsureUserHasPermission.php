<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        abort_if(! $user, 401);

        $hasAnyPermission = array_any(
            $permissions,
            fn (string $permission) => $user->hasPermission($permission)
        );

        abort_if(! $hasAnyPermission, 403);

        return $next($request);
    }
}
