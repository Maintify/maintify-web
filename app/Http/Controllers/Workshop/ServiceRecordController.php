<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRecordRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Services\VehicleHealthService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceRecordController extends Controller
{
    public function __construct(
        private readonly VehicleHealthService $healthService,
    ) {}

    /**
     * Resolve the workshop for the authenticated user (owner or staff).
     */
    private function resolveWorkshop(Request $request): ?Workshop
    {
        /** @var User $user */
        $user = $request->user();

        $workshopModel = $user->workshop;
        if ($workshopModel instanceof Workshop) {
            return $workshopModel;
        }

        $staffWorkshop = $user->workshopStaff?->workshop;

        return $staffWorkshop instanceof Workshop ? $staffWorkshop : null;
    }

    /**
     * Display a listing of the workshop's service records.
     */
    public function index(Request $request): View
    {
        $workshop = $this->resolveWorkshop($request);
        if (! $workshop) {
            abort(403, 'Workshop tidak ditemukan.');
        }

        $search = $request->input('search');
        $type = $request->input('type');

        $query = $workshop->serviceRecords()
            ->with(['vehicle', 'vehicle.owner', 'performedBy'])
            ->latest('service_date');

        if ($search) {
            $query->whereHas('vehicle', function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($type && array_key_exists($type, ServiceRecord::SERVICE_TYPES)) {
            $query->where('service_type', $type);
        }

        $serviceRecords = $query->paginate(15)->withQueryString();

        return view('workshop.service-records.index', [
            'serviceRecords' => $serviceRecords,
            'serviceTypes' => ServiceRecord::SERVICE_TYPES,
            'search' => $search,
            'type' => $type,
            'editLimitHours' => config('maintify.service_records.edit_limit_hours', 24),
        ]);
    }

    /**
     * Show the form to create a new service record for a given vehicle.
     *
     * Accessible after a successful QR scan (FR-091).
     * Vehicle ID passed as query string: ?vehicle_id=...
     */
    public function create(Request $request): View
    {
        $vehicle = Vehicle::findOrFail($request->query('vehicle_id'));
        /** @var User $user */
        $user = $request->user();

        $workshopModel = $user->workshop;
        /** @var Workshop|null $workshop */
        $workshop = $workshopModel instanceof Workshop ? $workshopModel : null;
        if (! $workshop) {
            $staffWorkshop = $user->workshopStaff?->workshop;
            $workshop = $staffWorkshop instanceof Workshop ? $staffWorkshop : null;
        }

        $spareparts = $workshop
            ? $workshop->spareparts()->where('is_active', true)->get(['name', 'category', 'price'])
            : collect();

        return view('workshop.service-records.create', [
            'vehicle' => $vehicle->load(['owner', 'serviceRecords' => function ($q) {
                $q->latest('service_date')->take(5);
            }]),
            'serviceTypes' => ServiceRecord::SERVICE_TYPES,
            'spareparts' => $spareparts,
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

        $workshopModel = $user->workshop;
        /** @var Workshop $workshop */
        $workshop = $workshopModel instanceof Workshop ? $workshopModel : $user->workshopStaff?->workshop;

        if (! $workshop) {
            abort(403, 'Workshop tidak ditemukan.');
        }

        /** @var Vehicle $vehicle */
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

    /**
     * Show the form for editing the specified service record.
     */
    public function edit(ServiceRecord $serviceRecord, Request $request): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $workshopModel = $user->workshop;
        /** @var Workshop|null $workshop */
        $workshop = $workshopModel instanceof Workshop ? $workshopModel : null;
        if (! $workshop) {
            $staffWorkshop = $user->workshopStaff?->workshop;
            $workshop = $staffWorkshop instanceof Workshop ? $staffWorkshop : null;
        }

        // Authorize
        if (! $workshop || $serviceRecord->workshop_id !== $workshop->id) {
            abort(403, 'Unauthorized.');
        }

        // Validate time limit
        $limitHours = config('maintify.service_records.edit_limit_hours', 24);
        if ($serviceRecord->created_at->addHours($limitHours)->isPast()) {
            return redirect()
                ->route('workshop.scan')
                ->with('error', "Batas waktu mengubah riwayat service telah habis ({$limitHours} jam).");
        }

        $spareparts = $workshop->spareparts()->where('is_active', true)->get(['name', 'category', 'price']);

        return view('workshop.service-records.edit', [
            'serviceRecord' => $serviceRecord->load('parts'),
            'vehicle' => $serviceRecord->vehicle,
            'serviceTypes' => ServiceRecord::SERVICE_TYPES,
            'spareparts' => $spareparts,
        ]);
    }

    /**
     * Update the specified service record in storage.
     */
    public function update(StoreServiceRecordRequest $request, ServiceRecord $serviceRecord): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $workshopModel = $user->workshop;
        /** @var Workshop|null $workshop */
        $workshop = $workshopModel instanceof Workshop ? $workshopModel : null;
        if (! $workshop) {
            $staffWorkshop = $user->workshopStaff?->workshop;
            $workshop = $staffWorkshop instanceof Workshop ? $staffWorkshop : null;
        }

        // Authorize
        if (! $workshop || $serviceRecord->workshop_id !== $workshop->id) {
            abort(403, 'Unauthorized.');
        }

        // Validate time limit
        $limitHours = config('maintify.service_records.edit_limit_hours', 24);
        if ($serviceRecord->created_at->addHours($limitHours)->isPast()) {
            return redirect()
                ->route('workshop.scan')
                ->with('error', "Batas waktu mengubah riwayat service telah habis ({$limitHours} jam).");
        }

        // Get old values for audit logging
        $oldData = $serviceRecord->only([
            'service_type',
            'service_date',
            'odometer_at_service',
            'mechanic_notes',
            'status',
            'total_cost',
        ]);

        // Update service record
        $serviceRecord->update([
            'service_type' => $request->validated('service_type'),
            'service_date' => $request->validated('service_date'),
            'odometer_at_service' => $request->validated('odometer_at_service'),
            'mechanic_notes' => $request->validated('mechanic_notes'),
            'status' => $request->validated('status'),
            'total_cost' => $request->validated('total_cost'),
        ]);

        $newData = $serviceRecord->only([
            'service_type',
            'service_date',
            'odometer_at_service',
            'mechanic_notes',
            'status',
            'total_cost',
        ]);

        // Determine changes
        $changes = [];
        foreach ($newData as $key => $val) {
            if ($oldData[$key] != $val) {
                if ($oldData[$key] instanceof Carbon && $val instanceof Carbon) {
                    if ($oldData[$key]->equalTo($val)) {
                        continue;
                    }
                }
                $changes[$key] = [
                    'old' => $oldData[$key] instanceof Carbon ? $oldData[$key]->toDateTimeString() : $oldData[$key],
                    'new' => $val instanceof Carbon ? $val->toDateTimeString() : $val,
                ];
            }
        }

        // Recreate spareparts
        $serviceRecord->parts()->delete();
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

        // Recalculate vehicle health and odometer
        /** @var Vehicle $vehicle */
        $vehicle = $serviceRecord->vehicle;
        $maxOdometerRecord = $vehicle->serviceRecords()->orderBy('odometer_at_service', 'desc')->first();
        $vehicle->current_odometer = $maxOdometerRecord
            ? $maxOdometerRecord->odometer_at_service
            : ($vehicle->getAttribute('initial_odometer') ?? 0);
        $vehicle->save();

        $latestRecord = $vehicle->serviceRecords()->latest('service_date')->first();
        if ($latestRecord instanceof ServiceRecord) {
            $this->healthService->updateAfterService($vehicle, $latestRecord);
        }

        // Log audit trail
        AuditLog::create([
            'actor_user_id' => $user->id,
            'action' => 'service_record.updated',
            'entity_type' => 'ServiceRecord',
            'entity_id' => $serviceRecord->getKey(),
            'metadata' => [
                'changes' => $changes,
                'vehicle_id' => $vehicle->getKey(),
            ],
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('workshop.service-records.index')
            ->with('success', 'Service record berhasil diperbarui.');
    }
}
