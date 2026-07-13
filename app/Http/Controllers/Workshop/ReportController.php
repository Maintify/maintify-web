<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\ServicePart;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Workshop;
use App\Services\ReportExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private ReportExportService $exportService) {}

    /**
     * Get the workshop for an admin user, or abort 403.
     */
    private function getOwnedWorkshop(Request $request): Workshop
    {
        /** @var User $user */
        $user = $request->user();
        $workshop = $user->workshop;

        if (! $workshop) {
            abort(403, 'Hanya admin bengkel yang dapat mengakses laporan operasional.');
        }

        return $workshop;
    }

    /**
     * Parse & validate the date range from request.
     * Defaults to the last 30 days if not provided.
     */
    private function parseDateRange(Request $request): array
    {
        $end = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $start = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        // Clamp: start must not be after end
        if ($start->gt($end)) {
            $start = (clone $end)->subDays(29)->startOfDay();
        }

        return [$start, $end];
    }

    /**
     * Build the aggregated report data array.
     */
    private function buildReportData(Workshop $workshop, Carbon $start, Carbon $end): array
    {
        $baseQuery = fn () => ServiceRecord::where('workshop_id', $workshop->id)
            ->whereBetween('service_date', [$start, $end]);

        // ─── Summary ─────────────────────────────────────────────────────────
        $totalServices = $baseQuery()->count();
        $totalRevenue = (float) $baseQuery()->sum('total_cost');
        $avgRevenue = $totalServices > 0 ? $totalRevenue / $totalServices : 0;

        // ─── Breakdown per Service Type ───────────────────────────────────────
        $typeBreakdown = $baseQuery()
            ->select('service_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_cost) as revenue'))
            ->groupBy('service_type')
            ->get()
            ->map(fn ($row) => [
                'type' => $row->service_type,
                'label' => ServiceRecord::SERVICE_TYPES[$row->service_type] ?? $row->service_type,
                'count' => $row->count,
                'revenue' => (float) $row->revenue,
                'revenue_formatted' => 'Rp '.number_format((float) $row->revenue, 0, ',', '.'),
            ]);

        // ─── Daily Timeline ───────────────────────────────────────────────────
        $daily = $baseQuery()
            ->select(
                DB::raw('DATE(service_date) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_cost) as revenue')
            )
            ->groupBy(DB::raw('DATE(service_date)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => $row->count,
                'revenue' => (float) $row->revenue,
                'revenue_formatted' => 'Rp '.number_format((float) $row->revenue, 0, ',', '.'),
            ]);

        // ─── Top 10 Spareparts ────────────────────────────────────────────────
        $serviceRecordIds = $baseQuery()->pluck('id');

        $topParts = ServicePart::whereIn('service_record_id', $serviceRecordIds)
            ->select(
                'part_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(quantity * unit_price) as total_value')
            )
            ->groupBy('part_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return [
            'period_label' => $start->format('d M Y').' – '.$end->format('d M Y'),
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'total_services' => $totalServices,
            'total_revenue' => $totalRevenue,
            'total_revenue_formatted' => 'Rp '.number_format($totalRevenue, 0, ',', '.'),
            'avg_revenue' => $avgRevenue,
            'avg_revenue_formatted' => 'Rp '.number_format($avgRevenue, 0, ',', '.'),
            'by_type' => $typeBreakdown,
            'daily' => $daily,
            'top_parts' => $topParts,
        ];
    }

    /**
     * Display the operational report index page.
     */
    public function index(Request $request): View
    {
        $workshop = $this->getOwnedWorkshop($request);
        [$start, $end] = $this->parseDateRange($request);
        $report = $this->buildReportData($workshop, $start, $end);

        return view('workshop.reports.index', compact('workshop', 'report'));
    }

    /**
     * Export the report as a CSV file download.
     */
    public function export(Request $request)
    {
        $workshop = $this->getOwnedWorkshop($request);
        [$start, $end] = $this->parseDateRange($request);
        $report = $this->buildReportData($workshop, $start, $end);

        $filename = 'laporan-'.str($workshop->name)->slug().'-'.$start->format('Ymd').'-'.$end->format('Ymd').'.csv';

        return $this->exportService->downloadCsv($report, $filename);
    }
}
