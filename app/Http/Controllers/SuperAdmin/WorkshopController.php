<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Workshop;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    /**
     * Tampilkan daftar seluruh bengkel di sistem dengan pencarian dan filter status.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Workshop::query()->with('user');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        $workshops = $query->latest()->paginate(10)->withQueryString();

        return view('super-admin.workshops.index', compact('workshops', 'search', 'status'));
    }

    /**
     * Tampilkan rincian detail bengkel beserta staf dan performa servicenya.
     */
    public function show(Workshop $workshop): View
    {
        $workshop->load(['user', 'staff.user', 'serviceRecords']);

        $serviceRecords = $workshop->serviceRecords;
        $totalServices = $serviceRecords->count();
        $totalEarnings = $serviceRecords->sum('total_cost');

        return view('super-admin.workshops.show', compact('workshop', 'totalServices', 'totalEarnings'));
    }

    /**
     * Perbarui status verifikasi dan status aktif/nonaktif dari bengkel.
     */
    public function update(Request $request, Workshop $workshop): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,revision_needed',
            'rejection_reason' => 'required_if:status,rejected,revision_needed|nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $status = $request->input('status');
        $isActive = $request->has('is_active') ? (bool) $request->input('is_active') : $workshop->is_active;

        $updateData = [
            'status' => $status,
            'is_active' => $isActive,
        ];

        if ($status === Workshop::STATUS_APPROVED) {
            $updateData['approved_at'] = now();
            $updateData['approved_by'] = auth()->id();
            $updateData['is_active'] = true;
            $updateData['rejection_reason'] = null;
        } elseif (in_array($status, [Workshop::STATUS_REJECTED, Workshop::STATUS_REVISION_NEEDED])) {
            $updateData['rejection_reason'] = $request->input('rejection_reason');
            $updateData['is_active'] = false;
        }

        $workshop->update($updateData);

        // Catat ke Audit Log
        AuditLog::record(
            'workshop_status_update',
            'workshops',
            $workshop->id,
            [
                'name' => $workshop->name,
                'status' => $status,
                'is_active' => $isActive,
            ]
        );

        return redirect()->back()->with('success', "Status dan informasi bengkel '{$workshop->name}' berhasil diperbarui.");
    }
}
