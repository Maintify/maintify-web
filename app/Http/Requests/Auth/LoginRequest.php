<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'login_as' => ['required', 'string', 'in:vehicle_owner,workshop,workshop_staff,super_admin'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'login_as.required' => 'Silakan pilih tipe akun sebelum masuk.',
            'login_as.in' => 'Tipe akun tidak valid.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('Email atau password salah.'),
            ]);
        }

        $user = Auth::user();

        // Validate role match
        $selectedRole = $this->input('login_as');
        $userRole = $user?->role;

        // Map: workshop_staff is allowed when 'workshop' is selected too
        $roleMap = [
            'vehicle_owner' => ['vehicle_owner'],
            'workshop' => ['workshop', 'workshop_staff'],
            'super_admin' => ['super_admin'],
        ];

        $allowedRoles = $roleMap[$selectedRole] ?? [];

        if (! in_array($userRole, $allowedRoles, true)) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            $label = match ($selectedRole) {
                'vehicle_owner' => 'Pelanggan',
                'workshop' => 'Bengkel',
                'super_admin' => 'Admin',
                default => 'yang dipilih',
            };

            throw ValidationException::withMessages([
                'login_as' => __('Akun ini bukan akun :role. Pastikan Anda memilih tipe akun yang sesuai.', [
                    'role' => $label,
                ]),
            ]);
        }

        if ($user && ! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Akun Anda telah dinonaktifkan.'),
            ]);
        }

        if ($user && $user->workshopStaff && ! $user->workshopStaff->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Akun staff Anda telah dinonaktifkan.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw new ThrottleRequestsException(
            __('Terlalu banyak percobaan masuk. Silakan coba lagi dalam :seconds detik.', [
                'seconds' => $seconds,
            ])
        );
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
