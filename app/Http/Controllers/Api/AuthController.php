<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CustomerAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Sanctum token auth for the Android app.
 *
 * The app signs up and signs in exactly as the website does, through
 * CustomerAccountService: an address is confirmed by a six digit code before
 * the account can be used. The only difference is what is handed back — the
 * phone gets a token where the browser gets a session.
 */
class AuthController extends Controller
{
    public function __construct(private CustomerAccountService $accounts) {}

    /**
     * Starts sign up. No token yet: the address has to be confirmed first,
     * which is the same rule the website follows.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'contact_number' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8', 'max:72'],
        ]);

        $user = $this->accounts->startRegistration($data);

        return response()->json([
            'status' => 'verification_sent',
            'email' => $user->email,
            'message' => 'We sent a 6 digit code to '.$user->email.'.',
        ], 201);
    }

    /** Confirms the emailed code and issues the token. */
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $user = $this->accounts->verifyCode($data['email'], $data['code']);

        return $this->issueToken($user, $data['device_name'] ?? null);
    }

    public function resend(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $this->accounts->resend($data['email']);

        // Always the same answer, so this cannot be used to discover which
        // addresses are registered.
        return response()->json(['status' => 'verification_sent']);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ]);

        $user = $this->accounts->attemptLogin($data['email'], $data['password']);

        if (! $user->email_verified_at) {
            // Send them back to the code step with a fresh code rather than a
            // dead end.
            $this->accounts->sendCode($user);

            return response()->json([
                'status' => 'verification_required',
                'email' => $user->email,
                'message' => 'Confirm your email first. We sent a new code.',
            ], 409);
        }

        return $this->issueToken($user, $data['device_name'] ?? null);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->accounts->present($request->user()));
    }

    public function logout(Request $request): JsonResponse
    {
        // Revokes only the token that made this call, so signing out on the
        // phone does not sign the customer out everywhere else.
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Signed out.']);
    }

    private function issueToken(User $user, ?string $deviceName): JsonResponse
    {
        return response()->json([
            'status' => 'signed_in',
            'token' => $user->createToken($deviceName ?: 'android')->plainTextToken,
            'user' => $this->accounts->present($user),
        ]);
    }
}
