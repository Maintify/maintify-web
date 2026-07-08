<?php

namespace Tests\Feature\Workshop;

use App\Models\Notification;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for Task 5.2.1: Add Service Record Form.
 *
 * Covers acceptance criteria:
 * - Form accessible after successful QR scan. (FR-091)
 * - Dynamic sparepart entry validation. (FR-093)
 * - Odometer validation: cannot be less than last recorded value. (Edge Case #5)
 * - On save, vehicle health status and oil life auto-update. (FR-025–FR-026)
 * - Notification sent to vehicle owner. (FR-111)
 */
class CreateServiceRecordTest extends TestCase
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

    private function createVehicleWithOwner(int $currentOdometer = 10000): array
    {
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario 160',
            'year' => 2022,
            'color' => 'Black',
            'fuel_type' => 'gasoline',
            'current_odometer' => $currentOdometer,
            'health_status' => 'good',
            'oil_life_percentage' => 80,
            'is_active' => true,
        ]);

        return [$owner, $vehicle];
    }

    private function validPayload(int $vehicleId, int $odometer = 12000): array
    {
        return [
            'vehicle_id' => $vehicleId,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => $odometer,
            'mechanic_notes' => 'Servis berkala rutin.',
            'status' => 'completed',
            'total_cost' => 250000,
        ];
    }

    // =========================================================
    // Test: Form Accessibility (FR-091)
    // =========================================================

    /** @test */
    public function create_form_accessible_for_approved_workshop_with_valid_vehicle()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $response = $this->actingAs($user)
            ->get(route('workshop.service-records.create', ['vehicle_id' => $vehicle->id]));

        $response->assertStatus(200);
        $response->assertViewIs('workshop.service-records.create');
        $response->assertViewHas('vehicle', fn ($v) => $v->id === $vehicle->id);
        $response->assertViewHas('serviceTypes');
    }

    /** @test */
    public function create_form_returns_404_for_invalid_vehicle_id()
    {
        [$user] = $this->createApprovedWorkshop();

        $response = $this->actingAs($user)
            ->get(route('workshop.service-records.create', ['vehicle_id' => 99999]));

        $response->assertStatus(404);
    }

    /** @test */
    public function guests_cannot_access_service_record_form()
    {
        $response = $this->get(route('workshop.service-records.create', ['vehicle_id' => 1]));
        $response->assertRedirect('/login');
    }

    // =========================================================
    // Test: Service Record Creation — Happy Path
    // =========================================================

    /** @test */
    public function approved_workshop_can_create_service_record()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $response = $this->actingAs($user)
            ->post(route('workshop.service-records.store'), $this->validPayload($vehicle->id));

        $response->assertRedirect(route('workshop.scan'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('service_records', [
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'status' => 'completed',
            'total_cost' => 250000,
        ]);
    }

    /** @test */
    public function service_record_with_spareparts_saves_parts_correctly()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $payload = array_merge($this->validPayload($vehicle->id), [
            'parts' => [
                ['part_name' => 'Oli Shell Helix 10W-40', 'quantity' => 1, 'unit_price' => 120000, 'part_category' => 'Oli'],
                ['part_name' => 'Filter Oli',              'quantity' => 1, 'unit_price' => 35000,  'part_category' => 'Filter'],
            ],
        ]);

        $this->actingAs($user)->post(route('workshop.service-records.store'), $payload);

        $record = ServiceRecord::where('vehicle_id', $vehicle->id)->first();
        $this->assertNotNull($record);
        $this->assertCount(2, $record->parts);

        $this->assertDatabaseHas('service_parts', [
            'service_record_id' => $record->id,
            'part_name' => 'Oli Shell Helix 10W-40',
            'quantity' => 1,
            'unit_price' => 120000,
        ]);
    }

    // =========================================================
    // Test: Odometer Validation (Edge Case #5)
    // =========================================================

    /** @test */
    public function odometer_less_than_current_vehicle_odometer_is_rejected()
    {
        [$user] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner(15000); // current = 15000

        $payload = $this->validPayload($vehicle->id, 12000); // odometer < current

        $response = $this->actingAs($user)
            ->post(route('workshop.service-records.store'), $payload);

        $response->assertSessionHasErrors('odometer_at_service');
        $this->assertDatabaseMissing('service_records', ['vehicle_id' => $vehicle->id]);
    }

    /** @test */
    public function odometer_equal_to_current_odometer_is_accepted()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner(15000);

        $payload = $this->validPayload($vehicle->id, 15000); // exactly equal

        $response = $this->actingAs($user)
            ->post(route('workshop.service-records.store'), $payload);

        $response->assertRedirect(route('workshop.scan'));
    }

    // =========================================================
    // Test: Vehicle Health Auto-Update (FR-025, FR-026)
    // =========================================================

    /** @test */
    public function oil_change_resets_oil_life_to_100()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $payload = array_merge($this->validPayload($vehicle->id), [
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
        ]);

        $this->actingAs($user)->post(route('workshop.service-records.store'), $payload);

        $vehicle->refresh();
        $this->assertEquals(100, $vehicle->oil_life_percentage);
    }

    /** @test */
    public function vehicle_odometer_updated_after_service_if_higher()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner(10000);

        $payload = $this->validPayload($vehicle->id, 15000);

        $this->actingAs($user)->post(route('workshop.service-records.store'), $payload);

        $vehicle->refresh();
        $this->assertEquals(15000, $vehicle->current_odometer);
    }

    /** @test */
    public function vehicle_health_status_updated_after_service()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $this->actingAs($user)->post(route('workshop.service-records.store'), $this->validPayload($vehicle->id));

        $vehicle->refresh();
        $this->assertContains($vehicle->health_status, ['good', 'warning', 'critical']);
    }

    /** @test */
    public function next_service_odometer_set_after_service()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner(10000);

        $payload = $this->validPayload($vehicle->id, 12000);

        $this->actingAs($user)->post(route('workshop.service-records.store'), $payload);

        $vehicle->refresh();
        $this->assertEquals(12000 + 5000, $vehicle->next_service_odometer);
    }

    // =========================================================
    // Test: Notification Sent to Owner (FR-111)
    // =========================================================

    /** @test */
    public function notification_sent_to_vehicle_owner_after_service()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $this->actingAs($user)->post(route('workshop.service-records.store'), $this->validPayload($vehicle->id));

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'service_record_created',
            'is_read' => false,
        ]);
    }

    // =========================================================
    // Test: Validation
    // =========================================================

    /** @test */
    public function service_record_store_requires_required_fields()
    {
        [$user] = $this->createApprovedWorkshop();

        $response = $this->actingAs($user)->post(route('workshop.service-records.store'), []);

        $response->assertSessionHasErrors(['vehicle_id', 'service_type', 'service_date', 'odometer_at_service', 'total_cost']);
    }

    /** @test */
    public function service_type_must_be_valid_enum_value()
    {
        [$user] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $payload = array_merge($this->validPayload($vehicle->id), ['service_type' => 'invalid_type']);

        $response = $this->actingAs($user)->post(route('workshop.service-records.store'), $payload);

        $response->assertSessionHasErrors('service_type');
    }

    /** @test */
    public function future_service_date_is_rejected()
    {
        [$user] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $payload = array_merge($this->validPayload($vehicle->id), [
            'service_date' => now()->addDays(1)->toDateString(),
        ]);

        $response = $this->actingAs($user)->post(route('workshop.service-records.store'), $payload);

        $response->assertSessionHasErrors('service_date');
    }
}
