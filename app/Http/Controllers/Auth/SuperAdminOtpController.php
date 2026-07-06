<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SuperAdminOtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the OTP verification form.
     */
    public function showVerifyForm(Request $request): RedirectResponse|View
    {
        if (! $request->session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp-verify');
    }

    /**
     * Verify the submitted OTP code.
     */
    public function verify(Request $request): RedirectResponse
    {
        if (! $request->session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]+$/'],
        ], [
            'otp.required' => 'Kode OTP harus diisi.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 digit.',
            'otp.regex' => 'Kode OTP harus berupa angka.',
        ]);

        $userId = $request->session()->get('otp_user_id');
        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        $result = $this->otpService->verifyOtp($user, $request->otp);

        if ($result['status'] === 'success') {
            // Authenticately login the user
            Auth::login($user);

            // Record success login audit log
            AuditLog::create([
                'actor_user_id' => $user->id,
                'action' => 'login_success',
                'ip_address' => $request->ip(),
                'metadata' => ['email' => $user->email],
            ]);

            // Clear temporary session variable
            $request->session()->forget('otp_user_id');

            // Regenerate session for security
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ($result['status'] === 'expired') {
            AuditLog::create([
                'actor_user_id' => $user->id,
                'action' => 'login_failed_expired_otp',
                'ip_address' => $request->ip(),
                'metadata' => ['email' => $user->email],
            ]);

            throw ValidationException::withMessages([
                'otp' => 'Kode OTP telah kedaluwarsa. Silakan klik "Kirim ulang OTP".',
            ]);
        }

        // Must be invalid status
        AuditLog::create([
            'actor_user_id' => $user->id,
            'action' => 'login_failed_invalid_otp',
            'ip_address' => $request->ip(),
            'metadata' => ['email' => $user->email],
        ]);

        throw ValidationException::withMessages([
            'otp' => 'Kode OTP tidak valid.',
        ]);
    }

    /**
     * Resend the OTP code.
     */
    public function resend(Request $request): RedirectResponse
    {
        if (! $request->session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $userId = $request->session()->get('otp_user_id');
        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        $this->otpService->generateAndSendOtp($user);

        AuditLog::create([
            'actor_user_id' => $user->id,
            'action' => 'login_attempt_otp_resent',
            'ip_address' => $request->ip(),
            'metadata' => ['email' => $user->email],
        ]);

        return redirect()->back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
    }
}
