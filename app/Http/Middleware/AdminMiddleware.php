<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Check if user is admin (you can customize this logic)
        // For now, we'll check if user has admin role or specific email
        $user = Auth::user();
        if (!$user->is_admin && !in_array($user->email, ['admin@carwash.com', 'admin@example.com'])) {
            abort(403, 'Unauthorized access to admin area.');
        }

        return $next($request);
    }
}
