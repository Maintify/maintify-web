<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function __construct(
        protected FileUploadService $fileUploadService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $query = Vehicle::where('user_id', auth()->id());

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('brand', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%')
                  ->orWhere('plate_number', 'like', '%' . $search . '%');
            });
        }

        $vehicles = $query->get();

        return view('vehicles.index', compact('vehicles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_url'] = $this->fileUploadService->uploadVehiclePhoto(
                $request->file('photo')
            );
        }

        $vehicle = Vehicle::create(array_merge($data, [
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle): View
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kendaraan ini.');
        }

        $serviceRecords = $vehicle->serviceRecords()
            ->with(['workshop', 'parts', 'performedBy'])
            ->orderBy('service_date', 'desc')
            ->get();

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

        return view('vehicles.show', compact(
            'vehicle',
            'serviceRecords',
            'totalServices',
            'totalCost',
            'avgInterval'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle): View
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke kendaraan ini.');
        }

        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        // Authorization is handled in UpdateVehicleRequest.
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($vehicle->photo_url) {
                $this->fileUploadService->delete($vehicle->photo_url);
            }

            $data['photo_url'] = $this->fileUploadService->uploadVehiclePhoto(
                $request->file('photo')
            );
        }

        $vehicle->update($data);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }
}

