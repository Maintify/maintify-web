<?php

namespace Tests\Feature\Vehicle;

use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createVehicleWithOwner(string $email = 'owner@maintify.app'): array
    {
        $owner = User::factory()->create([
            'email' => $email,
            'role' => User::ROLE_VEHICLE_OWNER,
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B '.rand(1000, 9999).' XYZ',
            'brand' => 'Honda',
            'model' => 'PCX 160',
            'year' => 2023,
            'fuel_type' => 'gasoline',
            'current_odometer' => 8000,
            'is_active' => true,
        ]);

        return [$owner, $vehicle];
    }

    /** @test */
    public function vehicle_owner_can_view_own_vehicle_service_history_and_stats()
    {
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $workshopUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $workshop = Workshop::create([
            'user_id' => $workshopUser->id,
            'name' => 'Bengkel ABC',
            'phone' => '081299998888',
            'email' => 'abc@bengkel.com',
            'address' => 'Jl. ABC No. 5',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        // Create 3 sequential service records (intervals of 10 days and 2500 km)
        ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $workshopUser->id,
            'service_type' => 'oil_change',
            'service_date' => '2026-06-01',
            'odometer_at_service' => 1000,
            'total_cost' => 50000,
            'status' => 'completed',
        ]);

        ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $workshopUser->id,
            'service_type' => 'periodic_service',
            'service_date' => '2026-06-11',
            'odometer_at_service' => 3000,
            'total_cost' => 150000,
            'status' => 'completed',
        ]);

        ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $workshopUser->id,
            'service_type' => 'repair',
            'service_date' => '2026-06-21',
            'odometer_at_service' => 6000,
            'total_cost' => 250000,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($owner)
            ->get(route('vehicles.service-history', $vehicle));

        $response->assertStatus(200);
        $response->assertViewIs('vehicles.service-history');

        // Check stats
        $response->assertViewHas('frequency', 3);
        $response->assertViewHas('avgOdoInterval', 2500.0);
        $response->assertViewHas('avgDaysInterval', 10.0);

        // Check record data
        $response->assertSee('Ganti Oli');
        $response->assertSee('Servis Berkala');
        $response->assertSee('Perbaikan');
    }

    /** @test */
    public function vehicle_owner_cannot_view_another_owners_vehicle_service_history()
    {
        [$owner1, $vehicle1] = $this->createVehicleWithOwner('owner1@maintify.app');
        [$owner2, $vehicle2] = $this->createVehicleWithOwner('owner2@maintify.app');

        $response = $this->actingAs($owner1)
            ->get(route('vehicles.service-history', $vehicle2));

        $response->assertStatus(403);
    }

    /** @test */
    public function vehicle_owner_can_filter_history_by_service_type_and_date_range()
    {
        [$owner, $vehicle] = $this->createVehicleWithOwner();

        $workshopUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $workshop = Workshop::create([
            'user_id' => $workshopUser->id,
            'name' => 'Bengkel ABC',
            'phone' => '081299998888',
            'email' => 'abc@bengkel.com',
            'address' => 'Jl. ABC No. 5',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $workshopUser->id,
            'service_type' => 'oil_change',
            'service_date' => '2026-06-01',
            'odometer_at_service' => 1000,
            'total_cost' => 50000,
            'status' => 'completed',
        ]);

        ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $workshopUser->id,
            'service_type' => 'periodic_service',
            'service_date' => '2026-06-15',
            'odometer_at_service' => 3000,
            'total_cost' => 150000,
            'status' => 'completed',
        ]);

        // Filter by service type
        $responseType = $this->actingAs($owner)
            ->get(route('vehicles.service-history', [$vehicle, 'service_type' => 'oil_change']));

        $recordsType = $responseType->original->getData()['serviceRecords'];
        $this->assertCount(1, $recordsType);
        $this->assertEquals('oil_change', $recordsType->first()->service_type);

        // Filter by date range (exclude the first record)
        $responseDate = $this->actingAs($owner)
            ->get(route('vehicles.service-history', [
                $vehicle,
                'start_date' => '2026-06-10',
                'end_date' => '2026-06-20',
            ]));

        $recordsDate = $responseDate->original->getData()['serviceRecords'];
        $this->assertCount(1, $recordsDate);
        $this->assertEquals('periodic_service', $recordsDate->first()->service_type);
    }
}
