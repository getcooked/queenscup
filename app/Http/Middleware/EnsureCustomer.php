<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Require a verified customer session for browser-only customer actions. */
class EnsureCustomer
{
    public function handle(Request $request, Closure $next)
    {
        $customerId = $request->session()->get('customer_user_id');
        $customer = $customerId ? User::find($customerId) : null;

        if (! $customer || $customer->role !== 'customer' || ! $customer->email_verified_at) {
            return $this->unauthenticated($request);
        }

        // Controllers must use this trusted account rather than customer
        // details supplied by the browser.
        $request->attributes->set('customer_user', $customer);

        return $next($request);
    }

    private function unauthenticated(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Please sign in to place a reservation.',
        ], 401);
    }
}
