<?php

namespace Tests\Feature\Vehicle;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_are_redirected_to_login()
    {
        $response = $this->get(route('vehicles.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_view_their_own_vehicles()
    {
        $user = User::factory()->vehicleOwner()->create();
        $this->actingAs($user);

        // Vehicle belonging to the user
        $myVehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'brand' => 'Honda',
            'model' => 'Vario 160',
            'plate_number' => 'B 1234 ABC',
        ]);

        // Vehicle belonging to another user
        $otherUser = User::factory()->vehicleOwner()->create();
        $otherVehicle = Vehicle::factory()->create([
            'user_id' => $otherUser->id,
            'brand' => 'Yamaha',
            'model' => 'NMAX',
            'plate_number' => 'B 9999 XYZ',
        ]);

        $response = $this->get(route('vehicles.index'));
        $response->assertStatus(200);
        $response->assertSee($myVehicle->brand);
        $response->assertSee($myVehicle->model);
        $response->assertSee($myVehicle->plate_number);

        $response->assertDontSee($otherVehicle->brand);
        $response->assertDontSee($otherVehicle->model);
        $response->assertDontSee($otherVehicle->plate_number);
    }

    /** @test */
    public function user_can_search_vehicles_by_brand_model_or_plate_number()
    {
        $user = User::factory()->vehicleOwner()->create();
        $this->actingAs($user);

        $vehicle1 = Vehicle::factory()->create([
            'user_id' => $user->id,
            'brand' => 'Honda',
            'model' => 'Vario',
            'plate_number' => 'B 1111 AAA',
        ]);

        $vehicle2 = Vehicle::factory()->create([
            'user_id' => $user->id,
            'brand' => 'Yamaha',
            'model' => 'Mio',
            'plate_number' => 'B 2222 BBB',
        ]);

        // Search by Brand
        $response = $this->get(route('vehicles.index', ['search' => 'Honda']));
        $response->assertSee($vehicle1->model);
        $response->assertDontSee($vehicle2->model);

        // Search by Model
        $response = $this->get(route('vehicles.index', ['search' => 'Mio']));
        $response->assertSee($vehicle2->brand);
        $response->assertDontSee($vehicle1->brand);

        // Search by Plate Number
        $response = $this->get(route('vehicles.index', ['search' => 'BBB']));
        $response->assertSee($vehicle2->brand);
        $response->assertDontSee($vehicle1->brand);
    }

    /** @test */
    public function it_displays_overall_empty_state_when_no_vehicles_registered()
    {
        $user = User::factory()->vehicleOwner()->create();
        $this->actingAs($user);

        $response = $this->get(route('vehicles.index'));
        $response->assertStatus(200);
        $response->assertSee('Anda belum memiliki kendaraan terdaftar');
        $response->assertSee('Tambah Kendaraan');
    }

    /** @test */
    public function it_displays_search_empty_state_when_no_search_results_found()
    {
        $user = User::factory()->vehicleOwner()->create();
        $this->actingAs($user);

        Vehicle::factory()->create([
            'user_id' => $user->id,
            'brand' => 'Honda',
            'model' => 'Vario',
            'plate_number' => 'B 1111 AAA',
        ]);

        $response = $this->get(route('vehicles.index', ['search' => 'Kawasaki']));
        $response->assertStatus(200);
        $response->assertSee('Pencarian Tidak Ditemukan');
        $response->assertSee('Reset Pencarian');
    }
}
