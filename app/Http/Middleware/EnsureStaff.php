<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class EnsureStaff
{
    public function handle(Request $request, Closure $next)
    {
        $staffId = $request->session()->get('staff_user_id');

        try {
            $staff = $staffId ? User::find($staffId) : null;
        } catch (QueryException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'We could not connect to the database. Please try again later.',
                ], 503);
            }

            return redirect()->route('login')
                ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
        }

        if (! $staff || ! in_array($staff->role, ['admin', 'cashier'], true)) {
            $request->session()->forget('staff_user_id');

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Please sign in with a staff account.'], 401);
            }

            return redirect()->guest(route('login'));
        }

        $request->attributes->set('staff_user', $staff);

        return $next($request);
    }
}
