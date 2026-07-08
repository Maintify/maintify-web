<?php

namespace Tests\Feature\Workshop;

use App\Models\QrCode;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for Task 5.1.2: QR Token Resolution Endpoint.
 *
 * Covers acceptance criteria:
 * - Valid token returns vehicle data + recent service history.
 * - Invalid/revoked token returns appropriate error.
 * - Unverified workshop attempting scan is rejected. (Edge Case #2)
 * - Scan log entry created (valid or invalid).
 */
class QrResolveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Create an approved workshop with its owner user.
     */
    private function createApprovedWorkshop(): array
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $workshop = Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Sejahtera',
            'phone' => '081234567890',
            'email' => 'sejahtera@bengkel.com',
            'address' => 'Jl. Sejahtera No. 10',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        return [$user, $workshop];
    }

    /**
     * Create a vehicle with an active QR Code.
     */
    private function createVehicleWithActiveQr(string $token = 'VALID_MNT_TOKEN'): array
    {
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario 160',
            'year' => 2022,
            'color' => 'Black',
            'fuel_type' => 'Gasoline',
            'current_odometer' => 12000,
            'health_status' => 'good',
            'oil_life_percentage' => 80,
            'is_active' => true,
        ]);

        $qrCode = QrCode::create([
            'vehicle_id' => $vehicle->id,
            'qr_token' => $token,
            'status' => QrCode::STATUS_ACTIVE,
            'issued_at' => now(),
        ]);

        return [$owner, $vehicle, $qrCode];
    }

    // =========================================================
    // Test: Valid Scan
    // =========================================================

    /** @test */
    public function approved_workshop_can_resolve_valid_qr_token_and_receives_vehicle_data()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle, $qrCode] = $this->createVehicleWithActiveQr();

        $response = $this->actingAs($user)->postJson(route('workshop.scan.resolve'), [
            'qr_token' => 'VALID_MNT_TOKEN',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'vehicle_id' => $vehicle->id,
                'plate_number' => 'B 1234 ABC',
                'brand' => 'Honda',
                'model' => 'Vario 160',
                'year' => 2022,
                'color' => 'Black',
                'fuel_type' => 'Gasoline',
                'current_odometer' => 12000,
                'health_status' => 'good',
                'oil_life_percentage' => 80,
                'owner_name' => $owner->name,
            ],
        ]);

        // Response must include recent_service_history key
        $response->assertJsonStructure([
            'status',
            'data' => [
                'vehicle_id',
                'brand',
                'model',
                'year',
                'plate_number',
                'vin',
                'color',
                'fuel_type',
                'current_odometer',
                'health_status',
                'oil_life_percentage',
                'owner_name',
                'recent_service_history',
            ],
        ]);
    }

    /** @test */
    public function valid_scan_logs_entry_with_is_valid_true()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle, $qrCode] = $this->createVehicleWithActiveQr();

        $this->actingAs($user)->postJson(route('workshop.scan.resolve'), [
            'qr_token' => 'VALID_MNT_TOKEN',
        ]);

        $this->assertDatabaseHas('qr_scan_logs', [
            'qr_code_id' => $qrCode->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'scanned_by_staff_id' => $user->id,
            'is_valid_scan' => true,
        ]);
    }

    /** @test */
    public function valid_scan_includes_recent_service_history()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle, $qrCode] = $this->createVehicleWithActiveQr();

        // Create service records
        ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 10000,
            'status' => ServiceRecord::STATUS_COMPLETED,
            'service_date' => now()->subDays(30),
            'total_cost' => 150000,
        ]);

        $response = $this->actingAs($user)->postJson(route('workshop.scan.resolve'), [
            'qr_token' => 'VALID_MNT_TOKEN',
        ]);

        $response->assertStatus(200);
        $history = $response->json('data.recent_service_history');
        $this->assertCount(1, $history);
        $this->assertEquals(ServiceRecord::TYPE_OIL_CHANGE, $history[0]['service_type']);
        $this->assertEquals(10000, $history[0]['odometer_at_service']);
    }

    // =========================================================
    // Test: Invalid QR
    // =========================================================

    /** @test */
    public function approved_workshop_scanning_invalid_qr_token_receives_error()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        $response = $this->actingAs($user)->postJson(route('workshop.scan.resolve'), [
            'qr_token' => 'TOTALLY_INVALID_TOKEN',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'code' => 'INVALID_QR',
            'message' => 'QR Code tidak dikenali',
        ]);
    }

    /** @test */
    public function invalid_qr_scan_logs_entry_with_is_valid_false()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        $this->actingAs($user)->postJson(route('workshop.scan.resolve'), [
            'qr_token' => 'TOTALLY_INVALID_TOKEN',
        ]);

        $this->assertDatabaseHas('qr_scan_logs', [
            'qr_code_id' => null,
            'vehicle_id' => null,
            'workshop_id' => $workshop->id,
            'scanned_by_staff_id' => $user->id,
            'is_valid_scan' => false,
        ]);
    }

    // =========================================================
    // Test: Revoked QR
    // =========================================================

    /** @test */
    public function approved_workshop_scanning_revoked_qr_token_receives_error()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithActiveQr();

        // Create a separate revoked QR code
        $revokedQr = QrCode::create([
            'vehicle_id' => $vehicle->id,
            'qr_token' => 'REVOKED_MNT_TOKEN',
            'status' => QrCode::STATUS_REVOKED,
            'issued_at' => now()->subDays(5),
            'revoked_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('workshop.scan.resolve'), [
            'qr_token' => 'REVOKED_MNT_TOKEN',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'code' => 'REVOKED_QR',
            'message' => 'QR Code ini sudah tidak aktif',
        ]);
    }

    /** @test */
    public function revoked_qr_scan_logs_entry_with_is_valid_false()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithActiveQr();

        $revokedQr = QrCode::create([
            'vehicle_id' => $vehicle->id,
            'qr_token' => 'REVOKED_MNT_TOKEN',
            'status' => QrCode::STATUS_REVOKED,
            'issued_at' => now()->subDays(5),
            'revoked_at' => now(),
        ]);

        $this->actingAs($user)->postJson(route('workshop.scan.resolve'), [
            'qr_token' => 'REVOKED_MNT_TOKEN',
        ]);

        $this->assertDatabaseHas('qr_scan_logs', [
            'qr_code_id' => $revokedQr->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'scanned_by_staff_id' => $user->id,
            'is_valid_scan' => false,
        ]);
    }

    // =========================================================
    // Test: Unverified Workshop (Edge Case #2)
    // =========================================================

    /** @test */
    public function pending_workshop_scanning_qr_is_rejected_with_unverified_error()
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Baru',
            'phone' => '081234567890',
            'email' => 'baru@bengkel.com',
            'address' => 'Jl. Baru No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_PENDING,
        ]);

        [, $vehicle] = $this->createVehicleWithActiveQr();

        // Bypass middleware to test controller logic directly
        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->postJson(route('workshop.scan.resolve'), [
                'qr_token' => 'VALID_MNT_TOKEN',
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'code' => 'UNVERIFIED_WORKSHOP',
        ]);
    }

    /** @test */
    public function rejected_workshop_scanning_qr_is_rejected_with_unverified_error()
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Ditolak',
            'phone' => '081234567890',
            'email' => 'tolak@bengkel.com',
            'address' => 'Jl. Tolak No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_REJECTED,
        ]);

        $this->createVehicleWithActiveQr();

        // Bypass middleware to test controller logic directly
        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->postJson(route('workshop.scan.resolve'), [
                'qr_token' => 'VALID_MNT_TOKEN',
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'code' => 'UNVERIFIED_WORKSHOP',
        ]);
    }

    /** @test */
    public function user_without_any_workshop_scanning_qr_is_rejected()
    {
        // A workshop-role user with no Workshop record at all
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $this->createVehicleWithActiveQr();

        $response = $this->actingAs($user)
            ->withoutMiddleware()
            ->postJson(route('workshop.scan.resolve'), [
                'qr_token' => 'VALID_MNT_TOKEN',
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'error',
            'code' => 'UNVERIFIED_WORKSHOP',
        ]);
    }

    // =========================================================
    // Test: Validation
    // =========================================================

    /** @test */
    public function resolve_endpoint_requires_qr_token_field()
    {
        [$user] = $this->createApprovedWorkshop();

        $response = $this->actingAs($user)->postJson(route('workshop.scan.resolve'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['qr_token']);
    }
}
