<?php

namespace Tests\Feature\Workshop;

use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NearbySearchTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->customer = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
        ]);
    }

    private function createWorkshop(array $attributes): Workshop
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        return Workshop::create(array_merge([
            'user_id' => $user->id,
            'name' => 'Bengkel Test',
            'phone' => '081234567890',
            'email' => 'test@bengkel.com',
            'address' => 'Jl. Test No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
            'rating_average' => 4.0,
        ], $attributes));
    }

    /** @test */
    public function vehicle_owner_can_search_workshops_sorted_by_distance()
    {
        // Coordinates near Jakarta Central (reference point)
        // Reference point: -6.193125, 106.772541

        // 1. Close Workshop (approx 0.1 km)
        $closeWorkshop = $this->createWorkshop([
            'name' => 'Bengkel Dekat',
            'latitude' => -6.193000,
            'longitude' => 106.772600,
            'rating_average' => 4.5,
        ]);

        // 2. Far Workshop (approx 15 km away)
        $farWorkshop = $this->createWorkshop([
            'name' => 'Bengkel Jauh',
            'latitude' => -6.323125,
            'longitude' => 106.882541,
            'rating_average' => 4.0,
        ]);

        // 3. Unverified Workshop (close but pending status)
        $unverifiedWorkshop = $this->createWorkshop([
            'name' => 'Bengkel Unverified',
            'latitude' => -6.193050,
            'longitude' => 106.772550,
            'status' => Workshop::STATUS_PENDING,
            'rating_average' => 4.8,
        ]);

        // 4. Inactive Workshop (close but inactive)
        $inactiveWorkshop = $this->createWorkshop([
            'name' => 'Bengkel Inactive',
            'latitude' => -6.193050,
            'longitude' => 106.772550,
            'is_active' => false,
            'rating_average' => 4.9,
        ]);

        $response = $this->actingAs($this->customer)
            ->getJson(route('api.workshops.nearby', [
                'latitude' => -6.193125,
                'longitude' => 106.772541,
                'radius' => 20, // 20 km radius
            ]));

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $data = $response->json('data');

        // Assert only verified and active workshops are returned
        $this->assertCount(2, $data);

        // Assert they are sorted by distance ascending (close workshop first)
        $this->assertEquals('Bengkel Dekat', $data[0]['name']);
        $this->assertEquals('Bengkel Jauh', $data[1]['name']);
        $this->assertLessThan(1.0, $data[0]['distance']);
        $this->assertGreaterThan(10.0, $data[1]['distance']);
    }

    /** @test */
    public function search_can_filter_by_rating()
    {
        // Close Workshop with high rating
        $highRating = $this->createWorkshop([
            'name' => 'Bengkel Bagus',
            'latitude' => -6.193000,
            'longitude' => 106.772600,
            'rating_average' => 4.8,
        ]);

        // Close Workshop with low rating
        $lowRating = $this->createWorkshop([
            'name' => 'Bengkel Biasa',
            'latitude' => -6.193100,
            'longitude' => 106.772500,
            'rating_average' => 3.2,
        ]);

        $response = $this->actingAs($this->customer)
            ->getJson(route('api.workshops.nearby', [
                'latitude' => -6.193125,
                'longitude' => 106.772541,
                'rating' => 4.0,
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals('Bengkel Bagus', $data[0]['name']);
    }

    /** @test */
    public function search_can_filter_by_service_type()
    {
        $workshopA = $this->createWorkshop([
            'name' => 'Bengkel A',
            'latitude' => -6.193000,
            'longitude' => 106.772600,
        ]);

        $workshopB = $this->createWorkshop([
            'name' => 'Bengkel B',
            'latitude' => -6.193100,
            'longitude' => 106.772500,
        ]);

        // Create a vehicle and service record for workshop B (oil_change)
        $vehicle = Vehicle::create([
            'user_id' => $this->customer->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'BeAT',
            'year' => 2021,
            'fuel_type' => 'gasoline',
            'current_odometer' => 5000,
        ]);

        ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshopB->id,
            'performed_by' => $workshopB->user_id,
            'service_type' => 'oil_change',
            'service_date' => now()->toDateString(),
            'odometer_at_service' => 5000,
            'total_cost' => 50000,
            'status' => 'completed',
        ]);

        // Search for workshops performing oil_change
        $response = $this->actingAs($this->customer)
            ->getJson(route('api.workshops.nearby', [
                'latitude' => -6.193125,
                'longitude' => 106.772541,
                'service_type' => 'oil_change',
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertCount(1, $data);
        $this->assertEquals('Bengkel B', $data[0]['name']);
    }

    /** @test */
    public function vehicle_owner_can_view_nearby_workshops_map_page()
    {
        $response = $this->actingAs($this->customer)
            ->get(route('workshops.nearby'));

        $response->assertStatus(200);
        $response->assertViewIs('workshops.nearby');
        $response->assertSee('Cari Bengkel');
    }

    /** @test */
    public function guest_cannot_view_nearby_workshops_map_page()
    {
        $response = $this->get(route('workshops.nearby'));

        $response->assertRedirect(route('login'));
    }
}
