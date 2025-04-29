<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
    public function handle($request, Closure $next, ...$roles) // Collect arguments as an array
{
    if (!Auth::check()) {
        // If the user is not logged in, redirect to login page
        return redirect()->route('login');
    }

    $user = Auth::user();

    // Ensure the user has a role and check if their role is in the allowed list
    if (!$user->role || !in_array($user->role->role_name, $roles)) {
        // If the user doesn't have a valid role, throw an unauthorized error
        abort(403, 'Unauthorized action.');
    }

    return $next($request);
}
}
