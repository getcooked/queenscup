<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $staff = $request->attributes->get('staff_user');

        if (! $staff || $staff->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Only admins can access this page.'], 403);
            }

            return redirect()->route('point-of-sales.index')
                ->withErrors(['authorization' => 'Only admins can access that page.']);
        }

        return $next($request);
    }
}
