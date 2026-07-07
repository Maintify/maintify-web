<?php

namespace Tests\Feature\Workshop;

use App\Models\QrCode;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrScanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function guests_are_redirected_to_login_from_scan_page()
    {
        $response = $this->get(route('workshop.scan'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function vehicle_owners_cannot_access_scan_page()
    {
        $user = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $response = $this->actingAs($user)->get(route('workshop.scan'));
        $response->assertStatus(403);
    }

    /** @test */
    public function pending_workshops_are_redirected_to_pending_page()
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        Workshop::create([
            'user_id' => $user->id,
            'name' => 'Pending Bengkel',
            'phone' => '081234567890',
            'email' => 'pending@bengkel.com',
            'address' => 'Jl. Pending No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)->get(route('workshop.scan'));
        $response->assertRedirect(route('workshop.pending'));
    }

    /** @test */
    public function approved_workshops_can_access_scan_page()
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Sejahtera',
            'phone' => '081234567890',
            'email' => 'sejahtera@bengkel.com',
            'address' => 'Jl. Sejahtera No. 10',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($user)->get(route('workshop.scan'));
        $response->assertStatus(200);
        $response->assertViewIs('workshop.scan');
    }

    /** @test */
    public function approved_workshop_can_resolve_valid_qr_token()
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

        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
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
            'qr_token' => 'VALID_MNT_TOKEN',
            'status' => QrCode::STATUS_ACTIVE,
            'issued_at' => now(),
        ]);

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
                'owner_name' => $owner->name,
            ],
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
    public function approved_workshop_scanning_invalid_qr_token_receives_error_and_logs_invalid()
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

        $response = $this->actingAs($user)->postJson(route('workshop.scan.resolve'), [
            'qr_token' => 'INVALID_TOKEN',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'code' => 'INVALID_QR',
            'message' => 'QR Code tidak dikenali',
        ]);

        $this->assertDatabaseHas('qr_scan_logs', [
            'qr_code_id' => null,
            'vehicle_id' => null,
            'workshop_id' => $workshop->id,
            'scanned_by_staff_id' => $user->id,
            'is_valid_scan' => false,
        ]);
    }

    /** @test */
    public function approved_workshop_scanning_revoked_qr_token_receives_error_and_logs_invalid()
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

        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
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

        $this->assertDatabaseHas('qr_scan_logs', [
            'qr_code_id' => $qrCode->id,
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'scanned_by_staff_id' => $user->id,
            'is_valid_scan' => false,
        ]);
    }
}
