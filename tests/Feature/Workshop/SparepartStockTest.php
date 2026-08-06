<?php

namespace Tests\Feature\Workshop;

use App\Models\Sparepart;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SparepartStockTest extends TestCase
{
    use RefreshDatabase;

    private User $workshopUser;
    private Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->workshopUser = User::factory()->create([
            'role' => User::ROLE_WORKSHOP,
        ]);

        $this->workshop = Workshop::create([
            'user_id' => $this->workshopUser->id,
            'name' => 'Bengkel Test Stok',
            'phone' => '081234567890',
            'email' => 'stok@bengkel.com',
            'address' => 'Jl. Test No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);
    }

    public function test_workshop_user_can_create_sparepart_with_stock(): void
    {
        $response = $this->actingAs($this->workshopUser)
            ->post(route('workshop.spareparts.store'), [
                'name' => 'Busi Iridium NGK',
                'category' => 'Busi',
                'price' => 75000,
                'stock' => 50,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('workshop.spareparts.index'));
        $this->assertDatabaseHas('spareparts', [
            'workshop_id' => $this->workshop->id,
            'name' => 'Busi Iridium NGK',
            'stock' => 50,
        ]);
    }

    public function test_workshop_user_can_update_sparepart_stock(): void
    {
        $part = Sparepart::create([
            'workshop_id' => $this->workshop->id,
            'name' => 'Oli Yamalube 0.8L',
            'category' => 'Oli',
            'price' => 45000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->workshopUser)
            ->put(route('workshop.spareparts.update', $part), [
                'name' => 'Oli Yamalube 0.8L',
                'category' => 'Oli',
                'price' => 50000,
                'stock' => 30,
                'is_active' => true,
            ]);

        $response->assertRedirect(route('workshop.spareparts.index'));
        $this->assertDatabaseHas('spareparts', [
            'id' => $part->id,
            'stock' => 30,
        ]);
    }

    public function test_service_record_creation_auto_deducts_sparepart_stock(): void
    {
        $part = Sparepart::create([
            'workshop_id' => $this->workshop->id,
            'name' => 'Kampas Rem Vario 150',
            'category' => 'Rem',
            'price' => 60000,
            'stock' => 20,
            'is_active' => true,
        ]);

        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 9999 STK',
            'brand' => 'Honda',
            'model' => 'Vario 150',
            'year' => 2022,
            'fuel_type' => 'gasoline',
            'current_odometer' => 10000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->workshopUser)
            ->post(route('workshop.service-records.store'), [
                'vehicle_id' => $vehicle->id,
                'service_type' => 'periodic_service',
                'service_date' => now()->toDateString(),
                'odometer_at_service' => 15000,
                'total_cost' => 120000,
                'status' => 'completed',
                'parts' => [
                    [
                        'part_name' => 'Kampas Rem Vario 150',
                        'quantity' => 4,
                        'unit_price' => 60000,
                        'part_category' => 'Rem',
                    ],
                ],
            ]);

        $response->assertRedirect(route('workshop.scan'));

        // Stock should be deducted from 20 to 16
        $this->assertDatabaseHas('spareparts', [
            'id' => $part->id,
            'stock' => 16,
        ]);
    }
}
