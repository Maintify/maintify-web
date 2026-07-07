<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\ServicePart;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the workshop admin dashboard with stats and reports.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var Workshop|null $workshop */
        $workshop = $user->workshop
            ?? $user->workshopStaff?->workshop;

        if ($workshop === null) {
            abort(404, 'Bengkel tidak ditemukan untuk akun ini.');
        }

        // 1. Service count stats (daily, weekly, monthly)
        $totalServices = $workshop->serviceRecords()->count();

        $dailyServices = $workshop->serviceRecords()
            ->whereDate('service_date', today())
            ->count();

        $weeklyServices = $workshop->serviceRecords()
            ->whereBetween('service_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $monthlyServices = $workshop->serviceRecords()
            ->whereMonth('service_date', now()->month)
            ->whereYear('service_date', now()->year)
            ->count();

        // 2. Active staff count
        $activeStaffCount = $workshop->staff()->where('is_active', true)->count();

        // 3. Chart: Vehicles served over time (last 7 days including today)
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $chartLabels[] = $day->translatedFormat('d M');
            $chartValues[] = $workshop->serviceRecords()
                ->whereDate('service_date', $day->toDateString())
                ->count();
        }

        // 4. Top spareparts summary (top 5 spareparts by total quantity)
        $topSpareparts = ServicePart::query()
            ->join('service_records', 'service_parts.service_record_id', '=', 'service_records.id')
            ->where('service_records.workshop_id', $workshop->id)
            ->select('service_parts.part_name', DB::raw('SUM(service_parts.quantity) as total_quantity'))
            ->groupBy('service_parts.part_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // 5. Active Customers
        $activeCustomers = $workshop->serviceRecords()
            ->join('vehicles', 'service_records.vehicle_id', '=', 'vehicles.id')
            ->distinct('vehicles.user_id')
            ->count('vehicles.user_id');

        // 6. Recent Services
        $recentServices = $workshop->serviceRecords()
            ->with(['vehicle', 'vehicle.owner'])
            ->orderByDesc('service_date')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'workshop',
            'totalServices',
            'dailyServices',
            'weeklyServices',
            'monthlyServices',
            'activeStaffCount',
            'chartLabels',
            'chartValues',
            'topSpareparts',
            'activeCustomers',
            'recentServices'
        ));
    }
}
