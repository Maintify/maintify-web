<?php

namespace Tests\Feature\Workshop;

use App\Models\ServicePart;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Models\WorkshopStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function workshop_user_with_pending_status_is_redirected_to_pending_page()
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        
        $workshop = Workshop::create([
            'user_id' => $user->id,
            'name' => 'Pending Bengkel',
            'phone' => '081234567890',
            'email' => 'pending@bengkel.com',
            'address' => 'Jl. Pending No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect(route('workshop.pending'));
    }

    /** @test */
    public function workshop_user_with_approved_status_can_access_dashboard_with_all_metrics()
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        
        $workshop = Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Sejahtera',
            'phone' => '081234567890',
            'email' => 'sejahtera@bengkel.com',
            'address' => 'Jl. Sejahtera No. 10',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        // Create some staff members
        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => User::factory()->create(['role' => User::ROLE_WORKSHOP])->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
            'joined_at' => now(),
        ]);
        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => User::factory()->create(['role' => User::ROLE_WORKSHOP])->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => false, // inactive staff
            'joined_at' => now(),
        ]);

        // Create a customer & vehicle
        $customer = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::create([
            'user_id' => $customer->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Beat',
            'year' => 2020,
            'color' => 'Black',
            'current_odometer' => 12000,
            'health_status' => 'good',
            'is_active' => true,
        ]);

        // Create service records for: Today (daily), This Week (weekly), This Month (monthly)
        // Today's service
        $serviceToday = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 12100,
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 100000.00,
            'service_date' => now(),
        ]);
        ServicePart::create([
            'service_record_id' => $serviceToday->id,
            'part_name' => 'Oli AHM SPX2',
            'quantity' => 2,
            'unit_price' => 50000.00,
            'part_category' => 'oil',
        ]);

        // Weekly service (e.g., 2 days ago, which is in the same week unless today is Monday/Sunday - but startOfWeek/endOfWeek handles it)
        $serviceWeekly = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_TUNE_UP,
            'odometer_at_service' => 12050,
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 80000.00,
            'service_date' => now()->startOfWeek()->addHours(12), // guaranteed within this week
        ]);
        ServicePart::create([
            'service_record_id' => $serviceWeekly->id,
            'part_name' => 'Busi Honda NGK',
            'quantity' => 1,
            'unit_price' => 25000.00,
            'part_category' => 'electrical',
        ]);

        // Monthly service (e.g., beginning of the month)
        $serviceMonthly = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $user->id,
            'service_type' => ServiceRecord::TYPE_BRAKE_SERVICE,
            'odometer_at_service' => 12000,
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 50000.00,
            'service_date' => now()->startOfMonth()->addHours(12), // guaranteed within this month
        ]);
        ServicePart::create([
            'service_record_id' => $serviceMonthly->id,
            'part_name' => 'Kampas Rem Depan',
            'quantity' => 1,
            'unit_price' => 45000.00,
            'part_category' => 'brakes',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);

        // Assert view contains correct metrics
        $response->assertViewHas('dailyServices');
        $response->assertViewHas('weeklyServices');
        $response->assertViewHas('monthlyServices');
        $response->assertViewHas('activeStaffCount');
        $response->assertViewHas('topSpareparts');
        $response->assertViewHas('chartLabels');
        $response->assertViewHas('chartValues');

        // Check top spareparts counts are correct
        $topParts = $response->viewData('topSpareparts');
        $this->assertCount(3, $topParts);
        $this->assertEquals('Oli AHM SPX2', $topParts->first()->part_name);
        $this->assertEquals(2, $topParts->first()->total_quantity);
    }

    /** @test */
    public function vehicle_owners_cannot_access_workshop_reports()
    {
        $user = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $response = $this->actingAs($user)->get('/workshop/reports');
        $response->assertStatus(403); // Forbidden by RoleMiddleware
    }

    /** @test */
    public function approved_workshop_users_can_access_workshop_reports()
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $workshop = Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Sejahtera',
            'phone' => '081234567890',
            'email' => 'sejahtera@bengkel.com',
            'address' => 'Jl. Sejahtera No. 10',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $response = $this->actingAs($user)->get('/workshop/reports');
        $response->assertStatus(200);
        $response->assertViewIs('workshop.reports.index');
    }
}
