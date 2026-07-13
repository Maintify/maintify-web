<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Tampilkan daftar seluruh kendaraan dalam sistem dengan filter pencarian.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Vehicle::query()->with('owner');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('chassis_number', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%")
                    ->orWhereHas('owner', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $vehicles = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.vehicles.index', compact('vehicles', 'search'));
    }

    /**
     * Tampilkan rincian spesifikasi kendaraan beserta riwayat servicenya (read-only).
     */
    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['owner', 'serviceRecords.workshop', 'serviceRecords.parts', 'serviceRecords.performedBy']);

        $serviceRecords = $vehicle->serviceRecords;
        $totalServices = $serviceRecords->count();
        $totalCost = $serviceRecords->sum('total_cost');

        $avgInterval = null;
        if ($totalServices > 1) {
            $recordsAsc = $serviceRecords->sortBy('service_date');
            $firstDate = $recordsAsc->first()->service_date;
            $lastDate = $recordsAsc->last()->service_date;
            $diffInDays = $firstDate->diffInDays($lastDate);
            $avgInterval = (int) round($diffInDays / ($totalServices - 1));
        }

        return view('super-admin.vehicles.show', compact(
            'vehicle',
            'serviceRecords',
            'totalServices',
            'totalCost',
            'avgInterval'
        ));
    }
}
