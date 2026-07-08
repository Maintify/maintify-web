<?php

namespace Tests\Feature\Workshop;

use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EditServiceRecordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

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

    /** @test */
    public function approved_workshop_can_access_edit_form_within_time_limit()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 12000,
            'total_cost' => 250000,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user)
            ->get(route('workshop.service-records.edit', $serviceRecord));

        $response->assertStatus(200);
        $response->assertViewIs('workshop.service-records.edit');
    }

    /** @test */
    public function approved_workshop_cannot_access_edit_form_after_time_limit()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        Carbon::setTestNow(now()->subHours(25));
        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 12000,
            'total_cost' => 250000,
            'status' => 'completed',
        ]);
        Carbon::setTestNow(); // restore time

        $response = $this->actingAs($user)
            ->get(route('workshop.service-records.edit', $serviceRecord));

        $response->assertRedirect(route('workshop.scan'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function workshop_cannot_edit_service_record_belonging_to_another_workshop()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        
        // Create another workshop
        $anotherUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $anotherWorkshop = Workshop::create([
            'user_id' => $anotherUser->id,
            'name' => 'Bengkel Lain',
            'phone' => '081234567891',
            'email' => 'lain@bengkel.com',
            'address' => 'Jl. Lain No. 11',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $anotherWorkshop->id, // belongs to another workshop
            'performed_by' => $anotherUser->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 12000,
            'total_cost' => 250000,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($user)
            ->get(route('workshop.service-records.edit', $serviceRecord));

        $response->assertStatus(403);
    }

    /** @test */
    public function approved_workshop_can_update_service_record_and_parts_within_time_limit()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 12000,
            'total_cost' => 250000,
            'status' => 'completed',
        ]);

        $serviceRecord->parts()->create([
            'part_name' => 'Old Part',
            'quantity' => 1,
            'unit_price' => 50000,
        ]);

        $payload = [
            'vehicle_id' => $vehicle->id,
            'service_type' => ServiceRecord::TYPE_REPAIR,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 13000,
            'total_cost' => 400000,
            'status' => 'completed',
            'mechanic_notes' => 'Updated notes.',
            'parts' => [
                ['part_name' => 'New Part 1', 'quantity' => 2, 'unit_price' => 75000, 'part_category' => 'Brake'],
            ]
        ];

        $response = $this->actingAs($user)
            ->put(route('workshop.service-records.update', $serviceRecord), $payload);

        $response->assertRedirect(route('workshop.scan'));
        $response->assertSessionHas('success');

        $serviceRecord->refresh();
        $this->assertEquals(ServiceRecord::TYPE_REPAIR, $serviceRecord->service_type);
        $this->assertEquals(13000, $serviceRecord->odometer_at_service);
        $this->assertEquals('Updated notes.', $serviceRecord->mechanic_notes);
        $this->assertEquals(400000, $serviceRecord->total_cost);

        $this->assertCount(1, $serviceRecord->parts);
        $this->assertDatabaseHas('service_parts', [
            'service_record_id' => $serviceRecord->id,
            'part_name' => 'New Part 1',
            'quantity' => 2,
            'unit_price' => 75000,
        ]);
        $this->assertDatabaseMissing('service_parts', [
            'part_name' => 'Old Part',
        ]);

        // Assert vehicle health stats re-run
        $vehicle->refresh();
        $this->assertEquals(13000, $vehicle->current_odometer);

        // Assert audit log was recorded
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $user->id,
            'action' => 'service_record.updated',
            'entity_type' => 'ServiceRecord',
            'entity_id' => $serviceRecord->id,
        ]);
    }

    /** @test */
    public function approved_workshop_cannot_update_service_record_after_time_limit()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        Carbon::setTestNow(now()->subHours(25));
        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 12000,
            'total_cost' => 250000,
            'status' => 'completed',
        ]);
        Carbon::setTestNow();

        $payload = [
            'vehicle_id' => $vehicle->id,
            'service_type' => ServiceRecord::TYPE_REPAIR,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 13000,
            'total_cost' => 400000,
            'status' => 'completed',
        ];

        $response = $this->actingAs($user)
            ->put(route('workshop.service-records.update', $serviceRecord), $payload);

        $response->assertRedirect(route('workshop.scan'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function update_odometer_can_be_less_than_vehicle_current_odometer_if_it_excludes_itself_but_not_prior_records()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();
        [$owner, $vehicle] = $this->createVehicleWithOwner(10000);

        // 1. Create first service record (odometer = 12000)
        $serviceRecord1 = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->subDays(2)->toDateString(),
            'odometer_at_service' => 12000,
            'total_cost' => 100000,
            'status' => 'completed',
        ]);

        // 2. Create second service record (odometer = 15000). Current vehicle odometer becomes 15000.
        $serviceRecord2 = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 15000,
            'total_cost' => 150000,
            'status' => 'completed',
        ]);
        $vehicle->update(['current_odometer' => 15000]);

        // Update second record: set odometer to 14000 (which is less than current 15000, but greater than first record 12000)
        $payload = [
            'vehicle_id' => $vehicle->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 14000,
            'total_cost' => 150000,
            'status' => 'completed',
        ];

        $response = $this->actingAs($user)
            ->put(route('workshop.service-records.update', $serviceRecord2), $payload);

        $response->assertRedirect(route('workshop.scan'));
        $response->assertSessionMissing('errors');

        $serviceRecord2->refresh();
        $this->assertEquals(14000, $serviceRecord2->odometer_at_service);

        // Update second record to 11000 (less than first record 12000) -> should be rejected!
        $payload['odometer_at_service'] = 11000;
        $response2 = $this->actingAs($user)
            ->put(route('workshop.service-records.update', $serviceRecord2), $payload);

        $response2->assertSessionHasErrors('odometer_at_service');
    }
}
