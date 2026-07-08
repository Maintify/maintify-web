<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\QrScanLog;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    /**
     * Show the QR code scanner page.
     */
    public function show(Request $request): View
    {
        return view('workshop.scan');
    }

    /**
     * Resolve a scanned QR token to vehicle data and log the scan.
     */
    public function resolve(Request $request): JsonResponse
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $token = $request->input('qr_token');
        /** @var User $user */
        $user = $request->user();

        /** @var Workshop|null $workshop */
        $workshop = $user->workshop
            ?? $user->workshopStaff?->workshop;

        // Edge Case #2: Reject if workshop is not approved (defense in depth)
        if (! $workshop || $workshop->status !== Workshop::STATUS_APPROVED) {
            return response()->json([
                'status' => 'error',
                'code' => 'UNVERIFIED_WORKSHOP',
                'message' => 'Bengkel Anda belum terverifikasi untuk melakukan scan QR Code',
            ], 403);
        }

        // Find the QR Code record by token
        $qrCode = QrCode::where('qr_token', $token)->first();

        // 1. If QR Code is invalid (not found in database)
        if (! $qrCode) {
            QrScanLog::create([
                'qr_code_id' => null,
                'vehicle_id' => null,
                'workshop_id' => $workshop->id,
                'scanned_by_staff_id' => $user->id,
                'is_valid_scan' => false,
                'scanned_at' => now(),
            ]);

            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_QR',
                'message' => 'QR Code tidak dikenali',
            ], 422);
        }

        // 2. If QR Code is revoked
        if ($qrCode->status === QrCode::STATUS_REVOKED) {
            QrScanLog::create([
                'qr_code_id' => $qrCode->id,
                'vehicle_id' => $qrCode->vehicle_id,
                'workshop_id' => $workshop->id,
                'scanned_by_staff_id' => $user->id,
                'is_valid_scan' => false,
                'scanned_at' => now(),
            ]);

            return response()->json([
                'status' => 'error',
                'code' => 'REVOKED_QR',
                'message' => 'QR Code ini sudah tidak aktif',
            ], 422);
        }

        // 3. If QR Code is active
        $vehicle = $qrCode->vehicle;

        if (! $vehicle) {
            return response()->json([
                'status' => 'error',
                'code' => 'VEHICLE_NOT_FOUND',
                'message' => 'Kendaraan tidak ditemukan',
            ], 404);
        }

        // Log the successful scan
        QrScanLog::create([
            'qr_code_id' => $qrCode->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'scanned_by_staff_id' => $user->id,
            'is_valid_scan' => true,
            'scanned_at' => now(),
        ]);

        // Fetch recent service history (last 5 records)
        $recentServices = $vehicle->serviceRecords()
            ->with('workshop:id,name')
            ->latest('service_date')
            ->take(5)
            ->get()
            ->map(fn (ServiceRecord $sr) => [
                'id' => $sr->id,
                'service_type' => $sr->service_type,
                'service_type_label' => $sr->service_type_label_readable,
                'service_date' => $sr->service_date?->toDateString(),
                'odometer_at_service' => $sr->odometer_at_service,
                'workshop_name' => $sr->workshop?->name,
                'mechanic_notes' => $sr->mechanic_notes,
                'total_cost' => $sr->total_cost,
            ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'vehicle_id' => $vehicle->id,
                'brand' => $vehicle->brand,
                'model' => $vehicle->model,
                'year' => $vehicle->year,
                'plate_number' => $vehicle->plate_number,
                'vin' => $vehicle->qr_code,
                'color' => $vehicle->color,
                'fuel_type' => $vehicle->fuel_type,
                'current_odometer' => $vehicle->current_odometer,
                'health_status' => $vehicle->health_status,
                'oil_life_percentage' => $vehicle->oil_life_percentage,
                'owner_name' => $vehicle->owner ? $vehicle->owner->name : 'N/A',
                'recent_service_history' => $recentServices,
            ],
        ]);
    }
}
