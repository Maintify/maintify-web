<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        try {
            $request->authenticate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($user && $user->isSuperAdmin()) {
                \App\Models\AuditLog::create([
                    'actor_user_id' => $user->id,
                    'action' => 'login_failed_invalid_password',
                    'ip_address' => $request->ip(),
                    'metadata' => ['email' => $request->email],
                ]);
            }
            throw $e;
        }

        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();

        if ($authenticatedUser->isSuperAdmin()) {
            // Record OTP Sent
            \App\Models\AuditLog::create([
                'actor_user_id' => $authenticatedUser->id,
                'action' => 'login_attempt_otp_sent',
                'ip_address' => $request->ip(),
                'metadata' => ['email' => $authenticatedUser->email],
            ]);

            // Generate and Send OTP
            $otpService = app(\App\Services\OtpService::class);
            $otpService->generateAndSendOtp($authenticatedUser);

            $userId = $authenticatedUser->id;

            // Log out immediately to prevent unauthorized access
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Store temporary user ID in session
            session(['otp_user_id' => $userId]);

            return redirect()->route('auth.otp.verify');
        }

        $request->session()->regenerate();

        // Workshop users with pending/rejected status → go to pending page
        if ($authenticatedUser->isWorkshop()) {
            /** @var Workshop|null $workshop */
            $workshop = $authenticatedUser->workshop;
            if (! $workshop || $workshop->status !== Workshop::STATUS_APPROVED) {
                return redirect()->route('workshop.pending');
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
