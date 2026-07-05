<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopPendingController extends Controller
{
    /**
     * Tampilkan halaman Pending Approval untuk bengkel yang belum diverifikasi.
     * Jika bengkel sudah approved, redirect ke dashboard.
     */
    public function show(Request $request): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $workshop = $user->workshop;

        // Jika user bukan workshop atau workshop sudah approved, redirect ke dashboard
        if (! $user->isWorkshop() || ($workshop && $workshop->isApproved())) {
            return redirect()->route('dashboard');
        }

        return view('workshop.pending', compact('workshop'));
    }
}
