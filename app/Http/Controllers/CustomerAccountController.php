<?php

namespace App\Http\Controllers;

use App\Mail\VerificationCodeMail;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ]);

        $existing = User::where('email', $data['email'])->first();

        if ($existing && $existing->role !== 'customer') {
            throw ValidationException::withMessages([
                'email' => 'That address is already used by a staff account.',
            ]);
        }

        if ($existing && $existing->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => 'That address is already registered. Sign in instead.',
            ]);
        }

        // An unverified account can be claimed again: the address owner is the
        // only one who can read the code, so this cannot take over a real one.
        $user = $existing ?: new User;

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'contact_number' => $data['contact_number'] ?? null,
            'role' => 'customer',
        ]);
        $user->password = Hash::make($data['password']);
        $user->save();

        $this->sendCode($user);

        return response()->json([
            'status' => 'verification_sent',
            'email' => $user->email,
            'message' => 'We sent a 6 digit code to '.$user->email.'.',
        ], 201);
    }

    public function resend(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $data['email'])->where('role', 'customer')->first();

        if (! $user || $user->email_verified_at) {
            // Say the same thing either way so this cannot be used to discover
            // which addresses are registered.
            return response()->json(['status' => 'verification_sent']);
        }

        $this->sendCode($user);

        return response()->json(['status' => 'verification_sent']);
    }

    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $data['email'])->where('role', 'customer')->firstOr(function () {
            throw ValidationException::withMessages(['code' => 'That code is not valid.']);
        });

        if ($user->email_verified_at) {
            return $this->signIn($request, $user);
        }

        $record = EmailVerificationCode::where('user_id', $user->id)->latest('id')->first();

        if (! $record || $record->isExpired()) {
            throw ValidationException::withMessages([
                'code' => 'That code has expired. Ask for a new one.',
            ]);
        }

        if ($record->isExhausted()) {
            throw ValidationException::withMessages([
                'code' => 'Too many wrong tries. Ask for a new code.',
            ]);
        }

        if (! $record->matches($data['code'])) {
            $record->increment('attempts');

            throw ValidationException::withMessages(['code' => 'That code is not correct.']);
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $record->delete();

        return $this->signIn($request, $user);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $data['email'])->where('role', 'customer')->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'That email and password do not match.',
            ]);
        }

        if (! $user->email_verified_at) {
            // Nudge them back into the code step rather than a dead end.
            $this->sendCode($user);

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

    private function sendCode(User $user): void
    {
        $code = EmailVerificationCode::issueFor($user);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user->name, $code));
        } catch (\Throwable $exception) {
            // A mail outage should not lose the account that was just created.
            // The code is still on record, so a resend will work once mail is
            // back, and the code is logged for local development.
            Log::warning('Could not send verification code', [
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            if (app()->environment('local', 'testing')) {
                Log::info("Verification code for {$user->email}: {$code}");
            }
        }
    }
}
