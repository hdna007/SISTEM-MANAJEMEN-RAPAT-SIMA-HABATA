<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Split role jika ada koma
        $roles = explode(',', implode(',', $roles));

        if (!in_array($user->role, $roles)) {
            return match ($user->role) {
                'sekretaris' => redirect('/approver'),
                'user' => redirect('/dashboard'),
                'admin' => redirect('/admin'),
                default => abort(403),
            };
        }

        return $next($request);
    }
}
