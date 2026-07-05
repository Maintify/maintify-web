<?php

namespace Tests\Feature\Vehicle;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditVehicleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Bypass Vite manifest missing error in testing environment
        $this->withoutVite();
    }

    public function test_guests_are_redirected_to_login()
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->get(route('vehicles.edit', $vehicle));
        $response->assertRedirect(route('login'));

        $response = $this->put(route('vehicles.update', $vehicle), []);
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_edit_page_for_their_own_vehicle()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('vehicles.edit', $vehicle));

        $response->assertStatus(200);
        $response->assertViewIs('vehicles.edit');
        $response->assertSee($vehicle->plate_number);
        $response->assertSee($vehicle->chassis_number);
    }

    public function test_user_cannot_view_edit_page_for_others_vehicle()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('vehicles.edit', $vehicle));

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_own_vehicle()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'brand' => 'Old Brand',
            'model' => 'Old Model',
        ]);

        $photo = UploadedFile::fake()->create('new-photo.jpg', 100, 'image/jpeg');

        $updateData = [
            'brand' => 'New Brand',
            'model' => 'New Model',
            'type' => 'New Type',
            'year' => 2024,
            'color' => 'Red',
            'fuel_type' => 'diesel',
            'engine_number' => 'NEWENG123',
            'current_odometer' => 15000,
            'photo' => $photo,
        ];

        $response = $this->actingAs($user)->put(route('vehicles.update', $vehicle), $updateData);

        $response->assertRedirect(route('vehicles.show', $vehicle));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'brand' => 'New Brand',
            'model' => 'New Model',
            'type' => 'New Type',
            'year' => 2024,
            'color' => 'Red',
            'fuel_type' => 'diesel',
            'engine_number' => 'NEWENG123',
            'current_odometer' => 15000,
        ]);

        $vehicle->refresh();
        $this->assertNotNull($vehicle->photo_url);
    }

    public function test_user_cannot_update_others_vehicle()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put(route('vehicles.update', $vehicle), [
            'brand' => 'New Brand',
            'model' => 'New Model',
            'year' => 2024,
            'fuel_type' => 'gasoline',
            'current_odometer' => 15000,
        ]);

        $response->assertStatus(403);
    }

    public function test_immutable_fields_cannot_be_changed()
    {
        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'plate_number' => 'B1234ABC',
            'chassis_number' => '12345678901234567',
        ]);

        $updateData = [
            'brand' => 'New Brand',
            'model' => 'New Model',
            'year' => 2024,
            'fuel_type' => 'gasoline',
            'current_odometer' => 15000,
            // Try to override immutable fields
            'plate_number' => 'B9999XYZ',
            'chassis_number' => '99999999999999999',
        ];

        $response = $this->actingAs($user)->put(route('vehicles.update', $vehicle), $updateData);

        $response->assertRedirect(route('vehicles.show', $vehicle));

        // Ensure database still has the original immutable values
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'plate_number' => 'B1234ABC',
            'chassis_number' => '12345678901234567',
            'brand' => 'New Brand', // Verify that the allowed fields did update
        ]);
    }
}
