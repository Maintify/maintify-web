<?php

namespace Tests\Feature\Workshop;

use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createApprovedWorkshop(string $name = 'Bengkel A', string $email = 'a@bengkel.com'): array
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $workshop = Workshop::create([
            'user_id' => $user->id,
            'name' => $name,
            'phone' => '0812'.rand(10000000, 99999999),
            'email' => $email,
            'address' => 'Jl. Jalan No. '.rand(1, 100),
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        return [$user, $workshop];
    }

    private function createVehicleWithOwner(string $ownerName, string $plateNumber): array
    {
        $owner = User::factory()->create([
            'name' => $ownerName,
            'role' => User::ROLE_VEHICLE_OWNER,
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => $plateNumber,
            'brand' => 'Yamaha',
            'model' => 'NMAX',
            'year' => 2020,
            'fuel_type' => 'gasoline',
            'current_odometer' => 10000,
            'is_active' => true,
        ]);

        return [$owner, $vehicle];
    }

    /** @test */
    public function approved_workshop_user_can_view_its_customer_directory()
    {
        [$user1, $workshop1] = $this->createApprovedWorkshop('Bengkel Maju', 'maju@bengkel.com');
        [$user2, $workshop2] = $this->createApprovedWorkshop('Bengkel Mundur', 'mundur@bengkel.com');

        [$owner1, $vehicle1] = $this->createVehicleWithOwner('Andi Pelanggan', 'B 1111 AAA');
        [$owner2, $vehicle2] = $this->createVehicleWithOwner('Budi Pelanggan', 'B 2222 BBB');

        // Customer 1 serviced at Workshop 1
        ServiceRecord::create([
            'vehicle_id' => $vehicle1->id,
            'workshop_id' => $workshop1->id,
            'performed_by' => $user1->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->subDays(5)->toDateString(),
            'odometer_at_service' => 10500,
            'total_cost' => 150000,
            'status' => 'completed',
        ]);

        // Customer 2 serviced at Workshop 2 only
        ServiceRecord::create([
            'vehicle_id' => $vehicle2->id,
            'workshop_id' => $workshop2->id,
            'performed_by' => $user2->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 12000,
            'total_cost' => 200000,
            'status' => 'completed',
        ]);

        // View as Workshop 1
        $response = $this->actingAs($user1)
            ->get(route('workshop.customers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('workshop.customers.index');
        $response->assertSee('Andi Pelanggan');
        $response->assertSee('B 1111 AAA');

        // Workshop 2's customer should not be seen
        $response->assertDontSee('Budi Pelanggan');
        $response->assertDontSee('B 2222 BBB');
    }

    /** @test */
    public function workshop_user_can_search_customers_by_name_and_plate_number()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        [$owner1, $vehicle1] = $this->createVehicleWithOwner('Charlie Cap', 'B 3333 CCC');
        [$owner2, $vehicle2] = $this->createVehicleWithOwner('David Doy', 'B 4444 DDD');

        ServiceRecord::create([
            'vehicle_id' => $vehicle1->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 5000,
            'total_cost' => 100000,
            'status' => 'completed',
        ]);

        ServiceRecord::create([
            'vehicle_id' => $vehicle2->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 8000,
            'total_cost' => 100000,
            'status' => 'completed',
        ]);

        // Search by name
        $responseName = $this->actingAs($user)
            ->get(route('workshop.customers.index', ['search' => 'Charlie']));
        $responseName->assertSee('Charlie Cap');
        $responseName->assertDontSee('David Doy');

        // Search by plate number
        $responsePlate = $this->actingAs($user)
            ->get(route('workshop.customers.index', ['search' => 'B 4444 DDD']));
        $responsePlate->assertSee('David Doy');
        $responsePlate->assertDontSee('Charlie Cap');
    }
}
