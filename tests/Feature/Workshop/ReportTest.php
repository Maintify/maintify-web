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

class ReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createApprovedWorkshopAdmin(): array
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $workshop = Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Laporan',
            'phone' => '081200000099',
            'email' => 'laporan@bengkel.com',
            'address' => 'Jl. Laporan No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'operational_hours' => 'Senin - Jumat (08:00 - 17:00)',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        return [$user, $workshop];
    }

    private function createServiceRecord(Workshop $workshop, array $overrides = []): ServiceRecord
    {
        // Create a vehicle owner and a vehicle if not provided in overrides
        $vehicleOwnerId = $overrides['vehicle_owner_user_id'] ?? null;
        if (! $vehicleOwnerId) {
            $vehicleOwner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
            $vehicleOwnerId = $vehicleOwner->id;
        }

        $vehicle = Vehicle::factory()->create(['user_id' => $vehicleOwnerId]);

        unset($overrides['vehicle_owner_user_id']);

        return ServiceRecord::create(array_merge([
            'workshop_id' => $workshop->id,
            'vehicle_id' => $vehicle->id,
            'performed_by' => $workshop->user_id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 50000,
            'mechanic_notes' => 'Test notes',
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 150000,
            'service_date' => now(),
        ], $overrides));
    }

    /** @test */
    public function approved_workshop_admin_can_view_report_page()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $response = $this->actingAs($admin)
            ->get(route('workshop.reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('workshop.reports.index');
        $response->assertViewHas('report');
        $response->assertViewHas('workshop');
    }

    /** @test */
    public function approved_workshop_admin_can_filter_report_by_date()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        // Record in range
        $this->createServiceRecord($workshop, [
            'service_date' => '2025-01-15',
            'total_cost' => 200000,
        ]);

        // Record out of range
        $this->createServiceRecord($workshop, [
            'service_date' => '2024-12-01',
            'total_cost' => 99000,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('workshop.reports.index', [
                'start_date' => '2025-01-01',
                'end_date' => '2025-01-31',
            ]));

        $response->assertStatus(200);

        $report = $response->viewData('report');

        $this->assertEquals(1, $report['total_services']);
        $this->assertEquals(200000, $report['total_revenue']);
    }

    /** @test */
    public function approved_workshop_admin_can_export_report_to_csv()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $record = $this->createServiceRecord($workshop, [
            'service_date' => now(),
            'total_cost' => 300000,
        ]);

        ServicePart::create([
            'service_record_id' => $record->id,
            'part_name' => 'Filter Oli',
            'quantity' => 2,
            'unit_price' => 75000,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('workshop.reports.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('.csv', $response->headers->get('content-disposition'));
    }

    /** @test */
    public function report_page_shows_correct_summary_totals()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $this->createServiceRecord($workshop, ['total_cost' => 100000, 'service_date' => now()]);
        $this->createServiceRecord($workshop, ['total_cost' => 200000, 'service_date' => now()]);
        $this->createServiceRecord($workshop, ['total_cost' => 300000, 'service_date' => now()]);

        $response = $this->actingAs($admin)
            ->get(route('workshop.reports.index'));

        $report = $response->viewData('report');

        $this->assertEquals(3, $report['total_services']);
        $this->assertEquals(600000, $report['total_revenue']);
        $this->assertEquals(200000, $report['avg_revenue']);
    }

    /** @test */
    public function non_admin_workshop_users_cannot_access_reports()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        $this->actingAs($staffUser)
            ->get(route('workshop.reports.index'))
            ->assertStatus(403);

        $this->actingAs($staffUser)
            ->get(route('workshop.reports.export'))
            ->assertStatus(403);
    }

    /** @test */
    public function guests_cannot_access_reports()
    {
        $this->get(route('workshop.reports.index'))
            ->assertRedirect(route('login'));

        $this->get(route('workshop.reports.export'))
            ->assertRedirect(route('login'));
    }
}
