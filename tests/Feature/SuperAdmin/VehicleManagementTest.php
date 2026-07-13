<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function super_admin_can_view_vehicles_list()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $owner1 = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $owner2 = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle1 = Vehicle::create([
            'user_id' => $owner1->id,
            'plate_number' => 'B 1111 AAA',
            'brand' => 'Honda',
            'model' => 'Vario',
            'year' => 2020,
            'is_active' => true,
        ]);

        $vehicle2 = Vehicle::create([
            'user_id' => $owner2->id,
            'plate_number' => 'D 2222 BBB',
            'brand' => 'Yamaha',
            'model' => 'NMax',
            'year' => 2021,
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.vehicles.index'));

        $response->assertStatus(200);
        $response->assertSee('B 1111 AAA');
        $response->assertSee('D 2222 BBB');
        $response->assertSee('Honda Vario');
        $response->assertSee('Yamaha NMax');
    }

    /** @test */
    public function super_admin_can_search_vehicles_by_plate_chassis_or_owner()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $owner1 = User::factory()->create(['name' => 'Andi', 'role' => User::ROLE_VEHICLE_OWNER]);
        $owner2 = User::factory()->create(['name' => 'Budi', 'role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle1 = Vehicle::create([
            'user_id' => $owner1->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario',
            'year' => 2020,
            'chassis_number' => 'CHASSISANDI123',
            'is_active' => true,
        ]);

        $vehicle2 = Vehicle::create([
            'user_id' => $owner2->id,
            'plate_number' => 'D 5678 DEF',
            'brand' => 'Yamaha',
            'model' => 'NMax',
            'year' => 2021,
            'chassis_number' => 'CHASSISBUDI456',
            'is_active' => true,
        ]);

        // Search by Plate
        $response = $this->actingAs($superAdmin)->get(route('admin.vehicles.index', ['search' => 'B 1234']));
        $response->assertStatus(200);
        $response->assertSee('B 1234 ABC');
        $response->assertDontSee('D 5678 DEF');

        // Search by Chassis
        $response = $this->actingAs($superAdmin)->get(route('admin.vehicles.index', ['search' => 'CHASSISBUDI456']));
        $response->assertStatus(200);
        $response->assertSee('D 5678 DEF');
        $response->assertDontSee('B 1234 ABC');

        // Search by Owner Name
        $response = $this->actingAs($superAdmin)->get(route('admin.vehicles.index', ['search' => 'Andi']));
        $response->assertStatus(200);
        $response->assertSee('B 1234 ABC');
        $response->assertDontSee('D 5678 DEF');
    }

    /** @test */
    public function super_admin_can_view_vehicle_details_and_service_history()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['name' => 'Andi', 'role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario',
            'year' => 2020,
            'chassis_number' => 'CHASSIS123',
            'engine_number' => 'ENGINE123',
            'is_active' => true,
        ]);

        $workshop = Workshop::create([
            'user_id' => User::factory()->create(['role' => User::ROLE_WORKSHOP])->id,
            'name' => 'Signature Garage',
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'service_type' => 'oil_change',
            'service_date' => now(),
            'odometer_at_service' => 12000,
            'total_cost' => 150000,
            'mechanic_notes' => 'Ganti oli mesin dan tune up rutin.',
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.vehicles.show', $vehicle->id));

        $response->assertStatus(200);
        $response->assertSee('B 1234 ABC');
        $response->assertSee('CHASSIS123');
        $response->assertSee('ENGINE123');
        $response->assertSee('Signature Garage');
        $response->assertSee('Ganti oli mesin dan tune up rutin.');
        $response->assertSee('Rp 150,000');
    }

    /** @test */
    public function super_admin_has_read_only_access_and_cannot_modify_vehicles()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario',
            'year' => 2020,
            'is_active' => true,
        ]);

        // Attempting to post to index (store) or put/patch/delete should be a 405 or 404
        $responseStore = $this->actingAs($superAdmin)->post('/admin/vehicles', [
            'plate_number' => 'B 9999 ZZZ',
        ]);
        $this->assertTrue($responseStore->status() === 404 || $responseStore->status() === 405);

        $responseUpdate = $this->actingAs($superAdmin)->put('/admin/vehicles/'.$vehicle->id, [
            'plate_number' => 'B 9999 ZZZ',
        ]);
        $this->assertTrue($responseUpdate->status() === 404 || $responseUpdate->status() === 405);

        $responseDelete = $this->actingAs($superAdmin)->delete('/admin/vehicles/'.$vehicle->id);
        $this->assertTrue($responseDelete->status() === 404 || $responseDelete->status() === 405);
    }

    /** @test */
    public function non_super_admin_cannot_access_vehicle_monitoring()
    {
        $regularUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario',
            'year' => 2020,
            'is_active' => true,
        ]);

        // Index
        $this->actingAs($regularUser)->get(route('admin.vehicles.index'))->assertStatus(403);

        // Show
        $this->actingAs($regularUser)->get(route('admin.vehicles.show', $vehicle->id))->assertStatus(403);
    }
}
