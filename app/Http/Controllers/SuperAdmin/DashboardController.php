<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Models\WorkshopVerification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tampilkan Super Admin Dashboard.
     */
    public function index(): View
    {
        // 1. Statistics
        $totalUsers = User::count();
        $totalVehicles = Vehicle::count();
        $totalWorkshops = Workshop::count();
        $totalServiceRecords = ServiceRecord::count();

        // Breakdown users by role
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        // Breakdown workshops by status
        $workshopsByStatus = Workshop::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 2. Growth chart: registrations per day for the last 7 days
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $chartLabels[] = $day->translatedFormat('d M');
            $chartValues[] = User::whereDate('created_at', $day->toDateString())->count();
        }

        // 3. Mock System Health
        $systemHealth = [
            'uptime' => '99.98%',
            'error_rate' => '0.04%',
            'api_requests' => AuditLog::count() * 12 + 1420, // dynamic but simulated
            'db_status' => 'Connected',
        ];

        // 4. Pending verification queue
        $pendingWorkshops = Workshop::where('status', Workshop::STATUS_PENDING)
            ->with('user')
            ->latest()
            ->get();

        $newUsersThisMonth = User::where('role', User::ROLE_VEHICLE_OWNER)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $pendingWorkshopsCount = $pendingWorkshops->count();

        return view('dashboard', compact(
            'totalUsers',
            'totalVehicles',
            'totalWorkshops',
            'totalServiceRecords',
            'usersByRole',
            'workshopsByStatus',
            'chartLabels',
            'chartValues',
            'systemHealth',
            'pendingWorkshops',
            'newUsersThisMonth',
            'pendingWorkshopsCount'
        ));
    }

    /**
     * Approve workshop registration.
     */
    public function approve(Workshop $workshop): RedirectResponse
    {
        // Update workshop status
        $workshop->update([
            'status' => Workshop::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
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

        return redirect()->back()->with('success', "Bengkel '{$workshop->name}' berhasil diverifikasi.");
    }

    /**
     * Reject workshop registration.
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

        return redirect()->back()->with('success', "Pendaftaran Bengkel '{$workshop->name}' ditolak.");
    }
}
