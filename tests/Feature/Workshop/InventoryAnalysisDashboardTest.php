<?php

namespace Tests\Feature\Workshop;

use App\Models\ServicePart;
use App\Models\ServiceRecord;
use App\Models\Sparepart;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAnalysisDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $workshopUser;

    protected Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->workshopUser = User::factory()->create([
            'role' => 'workshop',
            'is_active' => true,
        ]);

        $this->workshop = Workshop::create([
            'user_id' => $this->workshopUser->id,
            'name' => 'Bengkel Test Inventory',
            'phone' => '08123456789',
            'email' => 'workshop@test.com',
            'address' => 'Jl. Test No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'owner_name' => 'Owner Test',
            'owner_ktp_number' => '1234567890123456',
            'operational_hours' => 'Senin - Sabtu: 08:00 - 17:00',
            'status' => Workshop::STATUS_APPROVED,
            'is_active' => true,
        ]);
    }

    public function test_workshop_dashboard_displays_inventory_analysis_cards(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 9999 INV',
            'brand' => 'Honda',
            'model' => 'Vario 160',
            'year' => 2022,
            'color' => 'Black',
            'current_odometer' => 5000,
            'health_status' => 'good',
            'is_active' => true,
        ]);

        // Create Fast Moving sparepart (used 10 times)
        $serviceRecord1 = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $this->workshop->id,
            'performed_by' => $this->workshopUser->id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 5100,
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 500000.00,
            'service_date' => now()->subDays(5),
        ]);

        ServicePart::create([
            'service_record_id' => $serviceRecord1->id,
            'part_name' => 'Oli Mesin Honda MPX2',
            'quantity' => 10,
            'unit_price' => 50000,
            'part_category' => 'Oli',
        ]);

        // Create Dead Stock sparepart in catalog
        Sparepart::create([
            'workshop_id' => $this->workshop->id,
            'name' => 'Filter Udara Vario 160',
            'category' => 'Filter',
            'price' => 45000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->workshopUser)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Analisis Inventaris Sparepart');
        $response->assertSee('Fast Moving');
        $response->assertSee('Slow Moving');
        $response->assertSee('Dead Stock');
        $response->assertSee('Oli Mesin Honda MPX2');
        $response->assertSee('Filter Udara Vario 160');
        $response->assertSee('showModal = true');
        $response->assertSee('activePart');
        $response->assertDontSee('Akses Cepat');
        $response->assertDontSee('Top Sparepart');
    }

    public function test_period_filter_changes_inventory_analysis_data(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 8888 OLD',
            'brand' => 'Yamaha',
            'model' => 'NMAX',
            'year' => 2021,
            'color' => 'White',
            'current_odometer' => 12000,
            'health_status' => 'good',
            'is_active' => true,
        ]);

        // Create an old service record from 100 days ago
        $oldRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $this->workshop->id,
            'performed_by' => $this->workshopUser->id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 12100,
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 200000.00,
            'service_date' => now()->subDays(100),
        ]);

        ServicePart::create([
            'service_record_id' => $oldRecord->id,
            'part_name' => 'Busi NGK Spark Plug',
            'quantity' => 8,
            'unit_price' => 25000,
            'part_category' => 'Busi',
        ]);

        // In 30-day period filter for fast moving, 100-day old part should NOT be listed
        $response30 = $this->actingAs($this->workshopUser)
            ->get(route('dashboard', ['period_fast' => '30']));

        $response30->assertStatus(200);
        $response30->assertSee('Analisis Inventaris Sparepart');

        // In 180-day period filter for fast moving, 100-day old part SHOULD be included
        $response180 = $this->actingAs($this->workshopUser)
            ->get(route('dashboard', ['period_fast' => '180']));

        $response180->assertStatus(200);
        $response180->assertSee('Busi NGK Spark Plug');
    }
}
