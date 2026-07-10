<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tampilkan daftar pengguna platform dengan pencarian dan filter peran.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $role = $request->input('role');

        $query = User::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if (!empty($role)) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = User::ROLES;

        return view('super-admin.users.index', compact('users', 'roles', 'search', 'role'));
    }

    /**
     * Tampilkan detail spesifik dari pengguna.
     */
    public function show(User $user): View
    {
        $user->load(['vehicles', 'workshop', 'workshopStaff.workshop']);

        return view('super-admin.users.show', compact('user'));
    }

    /**
     * Perbarui status aktif/nonaktif akun pengguna.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $isActive = (bool) $request->input('is_active');

        // Cegah Super Admin menonaktifkan akun sendiri
        if ($user->id === auth()->id() && !$isActive) {
            return redirect()->back()->withErrors([
                'is_active' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.',
            ]);
        }

        // Update status aktif
        $user->update([
            'is_active' => $isActive,
        ]);

        // Catat ke Audit Log
        AuditLog::record(
            $isActive ? 'user_activate' : 'user_deactivate',
            'users',
            $user->id,
            [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        );

        $statusMsg = $isActive ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Akun pengguna '{$user->name}' berhasil {$statusMsg}.");
    }
}
