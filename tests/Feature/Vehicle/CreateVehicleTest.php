<?php

namespace Tests\Feature\Vehicle;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateVehicleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function vehicle_owner_can_create_vehicle()
    {
        $owner = User::factory()->vehicleOwner()->create();
        $this->actingAs($owner);

        $payload = [
            'plate_number' => 'B 1234 XYZ',
            'brand' => 'Honda',
            'model' => 'Vario 160',
            'type' => 'CBS',
            'year' => date('Y'),
            'color' => 'Hitam',
            'fuel_type' => 'gasoline',
            'engine_number' => 'EN123456',
            'chassis_number' => '1HGCM82633A004352',
            'current_odometer' => 0,
        ];

        $response = $this->post(route('vehicles.store'), $payload);
        $response->assertRedirect();
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => $payload['plate_number'],
            'user_id' => $owner->id,
        ]);
        $vehicle = Vehicle::first();
        $this->assertNotNull($vehicle->qr_code);
    }

    /** @test */
    public function duplicate_vin_or_plate_is_rejected()
    {
        $owner = User::factory()->vehicleOwner()->create();
        $this->actingAs($owner);

        Vehicle::factory()->create([
            'plate_number' => 'B 1234 XYZ',
            'chassis_number' => '1HGCM82633A004352',
        ]);

        $payload = [
            'plate_number' => 'B 1234 XYZ', // duplicate
            'brand' => 'Yamaha',
            'model' => 'NMAX',
            'type' => null,
            'year' => date('Y'),
            'color' => null,
            'fuel_type' => 'gasoline',
            'engine_number' => null,
            'chassis_number' => '1HGCM82633A004352', // duplicate VIN
            'current_odometer' => 0,
        ];

        $response = $this->post(route('vehicles.store'), $payload);
        $response->assertSessionHasErrors(['plate_number', 'chassis_number']);
    }
}
