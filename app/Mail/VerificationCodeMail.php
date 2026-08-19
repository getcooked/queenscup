<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * The six digit code that confirms a customer's email address.
 *
 * A Mailable rather than Mail::raw so the code is an inspectable property:
 * tests can read exactly what was sent instead of guessing at it.
 */
class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public string $code,
        public int $minutes = 10,
    ) {
    }

    public function build(): self
    {
        return $this->subject("Your Queen's Cup verification code")
            ->text('emails.verification-code', [
                'customerName' => $this->customerName,
                'code' => $this->code,
                'minutes' => $this->minutes,
            ]);
    }
}
