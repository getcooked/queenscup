<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CustomerAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Customer accounts.
 *
 * Registration creates the account straight away but leaves it unverified, so
 * a half finished sign up can be resumed by asking for a new code rather than
 * blocking the address forever. Nothing can be reserved until the address is
 * confirmed, which keeps a working contact against every order.
 */
class CustomerAccountController extends Controller
{
    public function __construct(private CustomerAccountService $accounts) {}

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ]);

        $user = $this->accounts->startRegistration($data);

        return response()->json([
            'status' => 'verification_sent',
            'email' => $user->email,
            'message' => 'We sent a 6 digit code to '.$user->email.'.',
        ], 201);
    }

    public function resend(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $this->accounts->resend($data['email']);

        // Same answer either way, so this cannot be used to discover
        // which addresses are registered.
        return response()->json(['status' => 'verification_sent']);
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $this->accounts->verifyCode($data['email'], $data['code']);

        return $this->signIn($request, $user);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = $this->accounts->attemptLogin($data['email'], $data['password']);

        if (! $user->email_verified_at) {
            // Nudge them back into the code step rather than a dead end.
            $this->accounts->sendCode($user);

            return response()->json([
                'status' => 'verification_required',
                'email' => $user->email,
                'message' => 'Confirm your email first. We sent a new code.',
            ], 409);
        }

        return $this->signIn($request, $user);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->session()->forget('customer_user_id');
        $request->session()->regenerate();

        return response()->json(['status' => 'signed_out']);
    }

    private function signIn(Request $request, User $user): JsonResponse
    {
        // New session id on sign in so a fixated one cannot be reused.
        $request->session()->regenerate();
        $request->session()->put('customer_user_id', $user->id);

        return response()->json([
            'status' => 'signed_in',
            'user' => [
                'id' => $user->id,
                'fullName' => $user->name,
                'email' => $user->email,
                'contactNumber' => $user->contact_number,
                'role' => 'customer',
            ],
        ]);
    }

}
