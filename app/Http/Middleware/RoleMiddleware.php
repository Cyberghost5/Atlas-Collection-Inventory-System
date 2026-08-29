<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this area.');
        }

        $user = Auth::user();

        if (empty($roles) || in_array($user->role, $roles)) {
            return $next($request);
        }

        // If customer tries to access admin area
        if ($user->isCustomer()) {
            return redirect()->route('shop.index')->with('error', 'Unauthorized access area.');
        }

        return redirect()->route('dashboard')->with('error', 'You do not have permission to access that section.');
    }
}
