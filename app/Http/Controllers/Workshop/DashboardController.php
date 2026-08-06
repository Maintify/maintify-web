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
        $startDate = now()->subDays(6)->startOfDay();
        $dailyCounts = $workshop->serviceRecords()
            ->where('service_date', '>=', $startDate)
            ->selectRaw('DATE(service_date) as date_val, COUNT(*) as aggregate_count')
            ->groupBy('date_val')
            ->pluck('aggregate_count', 'date_val');

        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dateStr = $day->toDateString();
            $chartLabels[] = $day->translatedFormat('d M');
            $chartValues[] = (int) ($dailyCounts[$dateStr] ?? 0);
        }

        // 4. Inventory Analysis (Fast Moving, Slow Moving, Dead Stock) with independent period parameters
        $periodFast = request('period_fast', request('period', '30'));
        $periodSlow = request('period_slow', request('period', '30'));
        $periodDead = request('period_dead', request('period', '30'));

        $getStartDate = function (string $p) {
            return match ($p) {
                '30' => now()->subDays(30),
                '90' => now()->subDays(90),
                '180' => now()->subDays(180),
                '365' => now()->subDays(365),
                default => null,
            };
        };

        // 4a. Fast Moving (based on periodFast)
        $fastStartDate = $getStartDate($periodFast);
        $fastQuery = DB::table('service_parts')
            ->join('service_records', 'service_parts.service_record_id', '=', 'service_records.id')
            ->where('service_records.workshop_id', $workshop->id);

        if ($fastStartDate !== null) {
            $fastQuery->where('service_records.service_date', '>=', $fastStartDate);
        }

        $fastUsed = $fastQuery
            ->select(
                'service_parts.part_name',
                'service_parts.part_category',
                DB::raw('SUM(service_parts.quantity) as total_quantity'),
                DB::raw('SUM(service_parts.quantity * service_parts.unit_price) as total_revenue')
            )
            ->groupBy('service_parts.part_name', 'service_parts.part_category')
            ->get();

        $fastMovingParts = $fastUsed
            ->where('total_quantity', '>=', 5)
            ->sortByDesc('total_quantity')
            ->values()
            ->take(5);

        if ($fastMovingParts->isEmpty()) {
            $fastMovingParts = $fastUsed
                ->where('total_quantity', '>', 0)
                ->sortByDesc('total_quantity')
                ->values()
                ->take(5);
        }

        // 4b. Slow Moving (based on periodSlow)
        $slowStartDate = $getStartDate($periodSlow);
        $slowQuery = DB::table('service_parts')
            ->join('service_records', 'service_parts.service_record_id', '=', 'service_records.id')
            ->where('service_records.workshop_id', $workshop->id);

        if ($slowStartDate !== null) {
            $slowQuery->where('service_records.service_date', '>=', $slowStartDate);
        }

        $slowUsed = $slowQuery
            ->select(
                'service_parts.part_name',
                'service_parts.part_category',
                DB::raw('SUM(service_parts.quantity) as total_quantity'),
                DB::raw('MAX(service_records.service_date) as last_used_date')
            )
            ->groupBy('service_parts.part_name', 'service_parts.part_category')
            ->get();

        $fastNamesForSlow = $fastMovingParts->pluck('part_name')->toArray();

        $slowMovingParts = $slowUsed
            ->reject(fn($item) => in_array($item->part_name, $fastNamesForSlow, true))
            ->sortBy('total_quantity')
            ->values()
            ->take(5);

        $catalogStockMap = $workshop->spareparts()
            ->get()
            ->keyBy(fn($p) => mb_strtolower(trim($p->name)));

        $fastMovingParts->each(function ($part) use ($catalogStockMap) {
            $matching = $catalogStockMap->get(mb_strtolower(trim($part->part_name)));
            $part->current_stock = $matching ? (int) $matching->stock : 0;
            $part->unit_price = $matching ? (float) $matching->price : 0;
        });

        $slowMovingParts->each(function ($part) use ($catalogStockMap) {
            $matching = $catalogStockMap->get(mb_strtolower(trim($part->part_name)));
            $part->current_stock = $matching ? (int) $matching->stock : 0;
            $part->unit_price = $matching ? (float) $matching->price : 0;
        });

        // 4c. Dead Stock (based on periodDead)
        $deadStartDate = $getStartDate($periodDead);
        $deadQuery = DB::table('service_parts')
            ->join('service_records', 'service_parts.service_record_id', '=', 'service_records.id')
            ->where('service_records.workshop_id', $workshop->id);

        if ($deadStartDate !== null) {
            $deadQuery->where('service_records.service_date', '>=', $deadStartDate);
        }

        $usedDeadNames = $deadQuery->pluck('service_parts.part_name')
            ->map(fn($n) => mb_strtolower(trim($n)))
            ->unique()
            ->toArray();

        $deadStockParts = $workshop->spareparts()
            ->where('is_active', true)
            ->get()
            ->reject(function ($sparepart) use ($usedDeadNames) {
                return in_array(mb_strtolower(trim($sparepart->name)), $usedDeadNames, true);
            })
            ->values()
            ->take(5);

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
            'periodFast',
            'periodSlow',
            'periodDead',
            'fastMovingParts',
            'slowMovingParts',
            'deadStockParts',
            'activeCustomers',
            'recentServices'
        ));
    }
}
