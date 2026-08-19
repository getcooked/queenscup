<?php

namespace App\Services;

use App\Mail\VerificationCodeMail;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * Customer sign up and sign in.
 *
 * The browser keeps a session and the phone keeps a token, but the rules
 * around an account are the same either way: an address is confirmed by a
 * six digit code before it can be used, codes expire, and too many wrong
 * tries burns the code. Keeping that here means the two front doors cannot
 * drift apart.
 */
class CustomerAccountService
{
    /**
     * Creates or reclaims an account and sends a fresh code.
     *
     * @param  array{name: string, email: string, password: string, contact_number?: ?string}  $data
     */
    public function startRegistration(array $data): User
    {
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

        // An unverified account can be claimed again: only the address owner
        // can read the code, so this cannot take over a real account.
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

        return $user;
    }

    /**
     * Checks a code and marks the address confirmed.
     *
     * An already verified account passes straight through so a repeated
     * submission is not an error.
     */
    public function verifyCode(string $email, string $code): User
    {
        $user = User::where('email', $email)->where('role', 'customer')->first();

        if (! $user) {
            throw ValidationException::withMessages(['code' => 'That code is not valid.']);
        }

        if ($user->email_verified_at) {
            return $user;
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

        if (! $record->matches($code)) {
            $record->increment('attempts');

            throw ValidationException::withMessages(['code' => 'That code is not correct.']);
        }

        $user->forceFill(['email_verified_at' => now()])->save();
        $record->delete();

        return $user;
    }

    /**
     * Sends another code, saying nothing about whether the address is known.
     */
    public function resend(string $email): void
    {
        $user = User::where('email', $email)->where('role', 'customer')->first();

        if ($user && ! $user->email_verified_at) {
            $this->sendCode($user);
        }
    }

    /**
     * Checks credentials. The returned account may still be unverified, which
     * the caller should handle by sending them back to the code step.
     */
    public function attemptLogin(string $email, string $password): User
    {
        $user = User::where('email', $email)->where('role', 'customer')->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'That email and password do not match.',
            ]);
        }

        return $user;
    }

    public function sendCode(User $user): void
    {
        $code = EmailVerificationCode::issueFor($user);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user->name, $code));
        } catch (\Throwable $exception) {
            // A mail outage should not lose the account that was just created.
            // The code is on record, so a resend works once mail is back, and
            // it is logged while developing.
            Log::warning('Could not send verification code', [
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            if (app()->environment('local', 'testing')) {
                Log::info("Verification code for {$user->email}: {$code}");
            }
        }
    }

    /** The shape both front doors hand back for a signed-in customer. */
    public function present(User $user): array
    {
        return [
            'id' => $user->id,
            'fullName' => $user->name,
            'email' => $user->email,
            'contactNumber' => $user->contact_number,
            'role' => 'customer',
        ];
    }
}
