<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class WorkshopRegistrationController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Tampilkan form pendaftaran bengkel (multi-step).
     */
    public function create(): View
    {
        return view('auth.register-workshop');
    }

    /**
     * Proses pendaftaran bengkel baru.
     * User & Workshop dibuat, OTP dikirim ke email untuk verifikasi.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Step 1: Informasi Pemilik
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'owner_ktp_number' => ['required', 'string', 'digits:16'],

            // Step 2: Informasi Bengkel
            'workshop_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(\+?62|0)[0-9]{8,13}$/'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'operational_hours' => ['required', 'string', 'max:255'],

            // Step 3: Dokumen Legalitas & Password
            'legal_document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // Max 10MB
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'owner_ktp_number.digits' => 'NIK KTP harus terdiri dari 16 digit angka.',
            'legal_document.max' => 'Ukuran dokumen legalitas maksimal adalah 10MB.',
            'legal_document.mimes' => 'Format dokumen harus berupa PDF, JPG, JPEG, atau PNG.',
        ]);

        // Handle legal document file upload
        $legalDocumentPath = null;
        if ($request->hasFile('legal_document')) {
            $legalDocumentPath = $request->file('legal_document')->store('documents', 'public');
        }

        // Buat user dengan role workshop & email_verified_at = null
        $user = User::create([
            'name' => $request->owner_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_WORKSHOP,
            'email_verified_at' => null,
        ]);

        // Buat data bengkel dengan status pending
        Workshop::create([
            'user_id' => $user->id,
            'name' => $request->workshop_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'province' => $request->province,
            'owner_name' => $request->owner_name,
            'owner_ktp_number' => $request->owner_ktp_number,
            'legal_document_url' => $legalDocumentPath,
            'operational_hours' => $request->operational_hours,
            'is_active' => false, // Tidak aktif sampai diapprove oleh Super Admin
            'status' => Workshop::STATUS_PENDING,
        ]);

        // Generate & Send OTP code to workshop owner email
        $this->otpService->generateAndSendOtp($user);

        // Put user ID into session for OTP verification
        $request->session()->put('otp_user_id', $user->id);

        return redirect()->route('auth.otp.verify')
            ->with('status', 'Pendaftaran bengkel berhasil disubmit! Kode OTP verifikasi telah dikirim ke email Anda. Silakan verifikasi kode OTP untuk mengaktifkan akun.');
    }
}
