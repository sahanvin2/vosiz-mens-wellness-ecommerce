<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SupplierMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (!$user) {
            abort(403, 'Access denied. Authentication required.');
        }
        
        if (!in_array($user->role, ['supplier', 'admin'])) {
            abort(403, 'Access denied. Supplier or Admin privileges required.');
        }

        return $next($request);
    }
}
