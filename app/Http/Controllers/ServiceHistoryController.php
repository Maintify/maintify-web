<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceHistoryController extends Controller
{
    /**
     * Display the service history timeline for a vehicle.
     */
    public function index(Vehicle $vehicle, Request $request): View
    {
        // Authorize: check if user owns the vehicle
        if ($vehicle->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized.');
        }

        // 1. Calculate General/Summary Statistics (on all records, unfiltered)
        $allRecords = $vehicle->serviceRecords()->orderBy('service_date', 'asc')->get();
        $frequency = $allRecords->count();
        $avgOdoInterval = null;
        $avgDaysInterval = null;

        if ($frequency > 1) {
            $totalOdoDiff = 0;
            $totalDaysDiff = 0;
            $intervalsCount = $frequency - 1;

            for ($i = 1; $i < $frequency; $i++) {
                $odoDiff = $allRecords[$i]->odometer_at_service - $allRecords[$i - 1]->odometer_at_service;
                $totalOdoDiff += max(0, $odoDiff);

                $datePrev = Carbon::parse($allRecords[$i - 1]->service_date);
                $dateCurr = Carbon::parse($allRecords[$i]->service_date);
                $daysDiff = $datePrev->diffInDays($dateCurr);
                $totalDaysDiff += max(0, $daysDiff);
            }

            $avgOdoInterval = round($totalOdoDiff / $intervalsCount);
            $avgDaysInterval = round($totalDaysDiff / $intervalsCount);
        }

        // 2. Query for filtered list (latest first for timeline layout)
        $query = $vehicle->serviceRecords()->with(['workshop', 'parts'])->orderBy('service_date', 'desc');

        // Apply filters
        $filters = [
            'service_type' => $request->input('service_type'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        if ($filters['service_type']) {
            $query->where('service_type', $filters['service_type']);
        }

        if ($filters['start_date']) {
            $query->where('service_date', '>=', $filters['start_date']);
        }

        if ($filters['end_date']) {
            $query->where('service_date', '<=', $filters['end_date']);
        }

        $serviceRecords = $query->paginate(10)->withQueryString();
        $serviceTypes = ServiceRecord::SERVICE_TYPES;

        return view('vehicles.service-history', compact(
            'vehicle',
            'serviceRecords',
            'frequency',
            'avgOdoInterval',
            'avgDaysInterval',
            'serviceTypes',
            'filters'
        ));
    }
}
