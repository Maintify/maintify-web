<?php

namespace Tests\Feature\Vehicle;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\ServiceRecord;
use App\Models\ServicePart;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function guests_are_redirected_to_login()
    {
        $vehicle = Vehicle::factory()->create();
        $response = $this->get(route('vehicles.show', $vehicle));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_view_their_own_vehicle_details()
    {
        $user = User::factory()->vehicleOwner()->create();
        $this->actingAs($user);

        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'brand' => 'Honda',
            'model' => 'Scoopy',
            'plate_number' => 'B 1234 XYZ',
            'current_odometer' => 12500,
            'health_score' => 95,
            'oil_life_percentage' => 88,
        ]);

        // Create a workshop
        $workshopOwner = User::factory()->workshop()->create();
        $workshop = Workshop::create([
            'user_id' => $workshopOwner->id,
            'name' => 'Bengkel Sejahtera',
            'email' => 'bengkel@test.com',
            'phone' => '0812345678',
            'address' => 'Jl. Sudirman No. 1',
            'status' => 'verified',
        ]);

        // Create a service record
        $mechanic = User::factory()->workshop()->create();
        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $mechanic->id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 12000,
            'mechanic_notes' => 'Ganti oli mesin Shell Advance',
            'status' => 'completed',
            'total_cost' => 150000,
            'service_date' => now()->subDays(10),
        ]);

        // Create a service part
        $part = ServicePart::create([
            'service_record_id' => $serviceRecord->id,
            'part_name' => 'Oli Shell Advance 10W-40',
            'quantity' => 1,
            'unit_price' => 150000,
            'part_category' => 'Oli',
        ]);

        $response = $this->get(route('vehicles.show', $vehicle));
        $response->assertStatus(200);

        // Check specifications display
        $response->assertSee('Honda');
        $response->assertSee('Scoopy');
        $response->assertSee('B 1234 XYZ');

        // Check statistics display
        $response->assertSee('Total Servis');
        $response->assertSee('1');
        $response->assertSee('Total Biaya Servis');
        $response->assertSee('Rp 150.000');

        // Check service timeline display
        $response->assertSee('Ganti Oli');
        $response->assertSee('Bengkel Sejahtera');
        $response->assertSee('Ganti oli mesin Shell Advance');

        // Check spareparts display
        $response->assertSee('Oli Shell Advance 10W-40');
        $response->assertSee('Oli');
    }

    /** @test */
    public function user_cannot_view_others_vehicle_details()
    {
        $user = User::factory()->vehicleOwner()->create();
        $otherUser = User::factory()->vehicleOwner()->create();

        $vehicle = Vehicle::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($user);
        $response = $this->get(route('vehicles.show', $vehicle));
        $response->assertStatus(403);
    }
}
