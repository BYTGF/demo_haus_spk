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
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            // Kalau belum login, lempar ke login page
            return redirect()->route('login');
        }

        $user = Auth::user();

        // dd($user->role->role_name);

        if (!$user->role || $user->role->role_name !== $role) {
            // Kalau user gak punya role atau role-nya salah
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
