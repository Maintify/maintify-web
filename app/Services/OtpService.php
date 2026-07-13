<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate and send a 6-digit OTP code to the Super Admin user.
     */
    public function generateAndSendOtp(User $user): string
    {
        $otp = $this->generateOtp();

        // Save in Cache for 5 minutes (300 seconds)
        Cache::put($this->getCacheKey($user->id), $otp, now()->addMinutes(5));

        // Delivery via Email
        Mail::to($user->email)->send(new OtpMail($otp));

        return $otp;
    }

    /**
     * Verify the user's OTP code.
     * Differentiates verification outcomes using status codes or array responses.
     * Returns:
     * - ['status' => 'success']
     * - ['status' => 'expired']
     * - ['status' => 'invalid']
     */
    public function verifyOtp(User $user, string $otp): array
    {
        $key = $this->getCacheKey($user->id);

        if (! Cache::has($key)) {
            return ['status' => 'expired'];
        }

        $cachedOtp = Cache::get($key);

        if (trim($otp) !== trim($cachedOtp)) {
            return ['status' => 'invalid'];
        }

        // OTP is correct! Clear it from Cache
        Cache::forget($key);

        return ['status' => 'success'];
    }

    /**
     * Generate a 6-digit numeric OTP code.
     */
    protected function generateOtp(): string
    {
        return strval(random_int(100000, 999999));
    }

    /**
     * Get the cache key for OTP.
     */
    protected function getCacheKey(int $userId): string
    {
        return "otp:{$userId}";
    }
}
