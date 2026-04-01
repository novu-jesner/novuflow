<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\RoleMiddleware;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle($request, Closure $next, $role)
{
  $user = auth()->user();

    if (!$user) {
        return redirect('/login');
    }

    if (strtolower($user->role) !== strtolower($role)) {
        abort(403); // better than redirect loop
    }

    return $next($request);
}
}
