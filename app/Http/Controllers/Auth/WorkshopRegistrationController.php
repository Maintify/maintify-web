<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class WorkshopRegistrationController extends Controller
{
    /**
     * Tampilkan form pendaftaran bengkel.
     */
    public function create(): View
    {
        return view('auth.register-workshop');
    }

    /**
     * Proses pendaftaran bengkel baru.
     * User & Workshop dibuat, status pending menunggu approval admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Akun
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            // Info Bengkel
            'workshop_name' => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:20'],
            'address'       => ['required', 'string', 'max:500'],
            'city'          => ['required', 'string', 'max:100'],
            'province'      => ['required', 'string', 'max:100'],
        ]);

        // Buat user dengan role workshop
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => User::ROLE_WORKSHOP,
        ]);

        // Buat data bengkel dengan status pending
        Workshop::create([
            'user_id'     => $user->id,
            'name'        => $request->workshop_name,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'city'        => $request->city,
            'province'    => $request->province,
            'is_active'   => false, // Tidak aktif sampai diapprove
            'status'      => 'pending',
        ]);

        // Login otomatis
        Auth::login($user);

        return redirect()->route('workshop.pending')
               ->with('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu verifikasi dari admin.');
    }
}
