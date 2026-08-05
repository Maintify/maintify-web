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
     * Display the service history timeline for user's vehicle(s).
     */
    public function index(Request $request, ?Vehicle $vehicle = null): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Check if route parameter vehicle is provided
        $selectedVehicle = null;
        if ($vehicle && $vehicle->exists) {
            if ($vehicle->user_id !== $user->id) {
                abort(403, 'Unauthorized.');
            }
            $selectedVehicle = $vehicle;
        } else {
            $filterVehicleId = $request->input('vehicle') ?? $request->input('vehicle_id');
            if ($filterVehicleId) {
                $requestedVehicle = Vehicle::find($filterVehicleId);
                if ($requestedVehicle && $requestedVehicle->user_id !== $user->id) {
                    abort(403, 'Unauthorized.');
                }
                $selectedVehicle = $requestedVehicle;
            }
        }

        $userVehicles = $user->vehicles()->orderBy('brand')->get();
        $userVehicleIds = $userVehicles->pluck('id');

        // Determine target vehicle IDs to query
        $targetVehicleIds = $selectedVehicle ? collect([$selectedVehicle->id]) : $userVehicleIds;

        // 1. Calculate General/Summary Statistics (unfiltered for target vehicles)
        $allRecords = ServiceRecord::whereIn('vehicle_id', $targetVehicleIds)
            ->orderBy('service_date', 'asc')
            ->get();

        $frequency = $allRecords->count();
        $totalCostAll = $allRecords->sum('total_cost');
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

        // 2. Query for list (latest first for timeline layout)
        $query = ServiceRecord::whereIn('vehicle_id', $targetVehicleIds)
            ->with(['vehicle', 'workshop', 'parts'])
            ->orderBy('service_date', 'desc');

        // Apply filters
        $filters = [
            'vehicle_id' => $selectedVehicle?->id ?? $request->input('vehicle_id'),
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

        // For backward compatibility with existing views/tests expecting $vehicle
        $vehicle = $selectedVehicle ?? ($userVehicles->first() ?? new Vehicle());

        return view('vehicles.service-history', compact(
            'vehicle',
            'selectedVehicle',
            'userVehicles',
            'serviceRecords',
            'frequency',
            'totalCostAll',
            'avgOdoInterval',
            'avgDaysInterval',
            'serviceTypes',
            'filters'
        ));
    }
}
