<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Workshop;
use App\Models\WorkshopVerification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkshopVerificationController extends Controller
{
    /**
     * Tampilkan antrean pendaftaran bengkel (status pending).
     */
    public function index(): View
    {
        $pendingWorkshops = Workshop::where('status', Workshop::STATUS_PENDING)
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('super-admin.workshops.pending', compact('pendingWorkshops'));
    }

    /**
     * Tampilkan halaman tinjauan detail bengkel.
     */
    public function show(Workshop $workshop): View
    {
        return view('super-admin.workshops.review', compact('workshop'));
    }

    /**
     * Setujui pendaftaran bengkel.
     */
    public function approve(Workshop $workshop): RedirectResponse
    {
        // Update workshop status
        $workshop->update([
            'status' => Workshop::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'is_active' => true,
        ]);

        // Create WorkshopVerification log
        WorkshopVerification::create([
            'workshop_id' => $workshop->id,
            'reviewed_by' => auth()->id(),
            'status' => WorkshopVerification::STATUS_APPROVED,
            'reviewed_at' => now(),
        ]);

        // Record in AuditLog
        AuditLog::record(
            'verify_workshop_approve',
            'workshops',
            $workshop->id,
            ['name' => $workshop->name, 'owner' => $workshop->owner_name]
        );

        // Send in-app notification
        Notification::create([
            'user_id' => $workshop->user_id,
            'type' => 'workshop_verification_approved',
            'title' => 'Pendaftaran Bengkel Disetujui',
            'message' => "Pendaftaran Bengkel '{$workshop->name}' telah disetujui. Akun Anda kini aktif.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.workshops.pending')
            ->with('success', "Bengkel '{$workshop->name}' berhasil diverifikasi.");
    }

    /**
     * Tolak pendaftaran bengkel.
     */
    public function reject(Request $request, Workshop $workshop): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Update workshop status
        $workshop->update([
            'status' => Workshop::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'is_active' => false,
        ]);

        // Create WorkshopVerification log
        WorkshopVerification::create([
            'workshop_id' => $workshop->id,
            'reviewed_by' => auth()->id(),
            'status' => WorkshopVerification::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
        ]);

        // Record in AuditLog
        AuditLog::record(
            'verify_workshop_reject',
            'workshops',
            $workshop->id,
            [
                'name' => $workshop->name,
                'owner' => $workshop->owner_name,
                'reason' => $request->rejection_reason,
            ]
        );

        // Send in-app notification
        Notification::create([
            'user_id' => $workshop->user_id,
            'type' => 'workshop_verification_rejected',
            'title' => 'Pendaftaran Bengkel Ditolak',
            'message' => "Pendaftaran Bengkel '{$workshop->name}' ditolak karena: {$request->rejection_reason}.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.workshops.pending')
            ->with('success', "Pendaftaran Bengkel '{$workshop->name}' ditolak.");
    }

    /**
     * Minta revisi/informasi tambahan untuk pendaftaran bengkel.
     */
    public function requestRevision(Request $request, Workshop $workshop): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Update workshop status
        $workshop->update([
            'status' => Workshop::STATUS_REVISION_NEEDED,
            'rejection_reason' => $request->rejection_reason,
            'is_active' => false,
        ]);

        // Create WorkshopVerification log
        WorkshopVerification::create([
            'workshop_id' => $workshop->id,
            'reviewed_by' => auth()->id(),
            'status' => WorkshopVerification::STATUS_REVISION_NEEDED,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_at' => now(),
        ]);

        // Record in AuditLog
        AuditLog::record(
            'verify_workshop_revision',
            'workshops',
            $workshop->id,
            [
                'name' => $workshop->name,
                'owner' => $workshop->owner_name,
                'reason' => $request->rejection_reason,
            ]
        );

        // Send in-app notification
        Notification::create([
            'user_id' => $workshop->user_id,
            'type' => 'workshop_verification_revision_needed',
            'title' => 'Revisi Pendaftaran Bengkel Diperlukan',
            'message' => "Pendaftaran Bengkel '{$workshop->name}' memerlukan revisi: {$request->rejection_reason}.",
            'is_read' => false,
        ]);

        return redirect()->route('admin.workshops.pending')
            ->with('success', "Permintaan revisi untuk Bengkel '{$workshop->name}' berhasil dikirim.");
    }
}
