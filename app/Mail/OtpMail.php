<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $otp)
    {
        //
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Kode OTP Masuk Maintify')
            ->html("Kode OTP verifikasi masuk Maintify Anda adalah: {$this->otp}. Kode ini berlaku selama 5 menit.");
    }
}
