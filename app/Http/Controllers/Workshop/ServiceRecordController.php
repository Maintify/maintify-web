<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRecordRequest;
use App\Models\Notification;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Services\VehicleHealthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceRecordController extends Controller
{
    public function __construct(
        private readonly VehicleHealthService $healthService,
    ) {}

    /**
     * Show the form to create a new service record for a given vehicle.
     *
     * Accessible after a successful QR scan (FR-091).
     * Vehicle ID passed as query string: ?vehicle_id=...
     */
    public function create(Request $request): View
    {
        $vehicle = Vehicle::findOrFail($request->query('vehicle_id'));

        return view('workshop.service-records.create', [
            'vehicle' => $vehicle->load(['owner', 'serviceRecords' => function ($q) {
                $q->latest('service_date')->take(5);
            }]),
            'serviceTypes' => ServiceRecord::SERVICE_TYPES,
        ]);
    }

    /**
     * Store a new service record.
     *
     * - Validates odometer (Edge Case #5)
     * - Saves service record + spareparts
     * - Auto-updates vehicle health stats (FR-025, FR-026)
     * - Sends notification to vehicle owner (FR-111)
     */
    public function store(StoreServiceRecordRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var Workshop $workshop */
        $workshop = $user->workshop ?? $user->workshopStaff?->workshop;

        $vehicle = Vehicle::findOrFail($request->validated('vehicle_id'));

        // Create the service record
        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => $request->validated('service_type'),
            'service_date' => $request->validated('service_date'),
            'odometer_at_service' => $request->validated('odometer_at_service'),
            'mechanic_notes' => $request->validated('mechanic_notes'),
            'status' => $request->validated('status'),
            'total_cost' => $request->validated('total_cost'),
        ]);

        // Save spareparts if any
        $parts = $request->validated('parts', []);
        if (! empty($parts)) {
            foreach ($parts as $part) {
                $serviceRecord->parts()->create([
                    'part_name' => $part['part_name'],
                    'quantity' => $part['quantity'],
                    'unit_price' => $part['unit_price'],
                    'part_category' => $part['part_category'] ?? null,
                ]);
            }
        }

        // Auto-update vehicle health stats (FR-025, FR-026)
        $this->healthService->updateAfterService($vehicle, $serviceRecord);

        // Send notification to vehicle owner (FR-111)
        if ($vehicle->owner) {
            $serviceLabel = ServiceRecord::SERVICE_TYPES[$serviceRecord->service_type] ?? $serviceRecord->service_type;
            Notification::create([
                'user_id' => $vehicle->owner->id,
                'type' => 'service_record_created',
                'title' => 'Service Baru Dicatat',
                'message' => "Kendaraan {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}) telah menjalani {$serviceLabel} di {$workshop->name}.",
                'is_read' => false,
            ]);
        }

        return redirect()
            ->route('workshop.scan')
            ->with('success', 'Service record berhasil disimpan.');
    }
}
