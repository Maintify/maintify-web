<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:vehicle_owner,workshop'],
        ], [
            'email.unique' => 'Email sudah terdaftar. Silakan gunakan email lain atau masuk ke akun yang ada.',
        ]);

        $role = $request->input('role', User::ROLE_VEHICLE_OWNER);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'email_verified_at' => null,
        ]);

        event(new Registered($user));

        // Generate & Send 6-digit OTP code to user's registered email
        $this->otpService->generateAndSendOtp($user);

        // Put user ID into session for OTP verification
        $request->session()->put('otp_user_id', $user->id);

        return redirect()->route('auth.otp.verify')
            ->with('status', 'Kode OTP verifikasi akun telah dikirim ke email Anda. Silakan masukkan kode untuk mengaktifkan akun.');
    }
}
