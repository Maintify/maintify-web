<?php

namespace Tests\Feature;

use App\Models\QrCode;
use App\Models\QrScanLog;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrScanLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_and_retrieve_a_qr_scan_log_with_relationships()
    {
        // 1. Create dependencies
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $staff = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $staff->id,
            'name' => 'Bengkel Test',
            'phone' => '08123456789',
            'email' => 'bengkel@test.com',
            'address' => 'Jl. Test No. 123',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario 160',
            'year' => 2022,
            'color' => 'Black',
            'current_odometer' => 12000,
            'health_status' => 'good',
            'is_active' => true,
        ]);

        $qrCode = QrCode::create([
            'vehicle_id' => $vehicle->id,
            'qr_token' => 'VALID_TOKEN_123',
            'status' => QrCode::STATUS_ACTIVE,
            'issued_at' => now(),
        ]);

        // 2. Create QR Scan Log
        $scanLog = QrScanLog::create([
            'qr_code_id' => $qrCode->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'scanned_by_staff_id' => $staff->id,
            'is_valid_scan' => true,
            'scanned_at' => now(),
        ]);

        // 3. Assertions
        $this->assertDatabaseHas('qr_scan_logs', [
            'id' => $scanLog->id,
            'qr_code_id' => $qrCode->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'scanned_by_staff_id' => $staff->id,
            'is_valid_scan' => true,
        ]);

        // Check relationships
        $this->assertEquals($qrCode->id, $scanLog->qrCode->id);
        $this->assertEquals($vehicle->id, $scanLog->vehicle->id);
        $this->assertEquals($workshop->id, $scanLog->workshop->id);
        $this->assertEquals($staff->id, $scanLog->scannedByStaff->id);

        // Check reverse relationships
        $this->assertTrue($qrCode->scanLogs->contains($scanLog));
        $this->assertTrue($vehicle->scanLogs->contains($scanLog));
        $this->assertTrue($workshop->scanLogs->contains($scanLog));
        $this->assertTrue($staff->scanLogs->contains($scanLog));
    }
}
