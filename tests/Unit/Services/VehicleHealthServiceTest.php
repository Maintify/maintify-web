<?php

namespace Tests\Unit\Services;

use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Services\VehicleHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for VehicleHealthService (Subtask 5.2.1a).
 *
 * Covers acceptance criteria:
 * - Oil change resets oil_life to 100%.
 * - Health status updated based on business rules (time since last service, odometer interval).
 */
class VehicleHealthServiceTest extends TestCase
{
    use RefreshDatabase;

    private VehicleHealthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VehicleHealthService;
    }

    // =========================================================
    // Helpers
    // =========================================================

    private function makeVehicle(array $attrs = []): Vehicle
    {
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        return Vehicle::create(array_merge([
            'user_id' => $owner->id,
            'plate_number' => 'B 9999 TST',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'year' => 2021,
            'color' => 'White',
            'fuel_type' => 'gasoline',
            'current_odometer' => 10000,
            'health_status' => 'good',
            'oil_life_percentage' => 80,
            'is_active' => true,
        ], $attrs));
    }

    private function makeWorkshop(): Workshop
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        return Workshop::create([
            'user_id' => $user->id,
            'name' => 'Test Bengkel',
            'phone' => '081100001111',
            'email' => 'test@bengkel.com',
            'address' => 'Jl. Test No.1',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);
    }

    private function makeServiceRecord(Vehicle $vehicle, array $attrs = []): ServiceRecord
    {
        $workshop = $this->makeWorkshop();

        return ServiceRecord::create(array_merge([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $workshop->user_id,
            'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
            'service_date' => now(),
            'odometer_at_service' => 12000,
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 200000,
        ], $attrs));
    }

    // =========================================================
    // Test: Oil Life Calculation
    // =========================================================

    /** @test */
    public function oil_change_results_in_100_oil_life_after_update()
    {
        $vehicle = $this->makeVehicle(['oil_life_percentage' => 20]);
        $record = $this->makeServiceRecord($vehicle, [
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 12000,
        ]);

        $this->service->updateAfterService($vehicle, $record);

        $vehicle->refresh();
        $this->assertEquals(100, $vehicle->oil_life_percentage);
    }

    /** @test */
    public function non_oil_change_service_does_not_reset_oil_life_to_100()
    {
        $vehicle = $this->makeVehicle(['oil_life_percentage' => 80]);
        $record = $this->makeServiceRecord($vehicle, [
            'service_type' => ServiceRecord::TYPE_TUNE_UP,
            'odometer_at_service' => 12000,
        ]);

        $this->service->updateAfterService($vehicle, $record);

        $vehicle->refresh();
        $this->assertNotEquals(100, $vehicle->oil_life_percentage);
    }

    // =========================================================
    // Test: Health Status Calculation
    // =========================================================

    /** @test */
    public function health_status_is_good_after_fresh_service_with_good_oil()
    {
        $vehicle = $this->makeVehicle([
            'oil_life_percentage' => 80,
            'next_service_odometer' => 15000,
        ]);

        // Oil change → oil life 100%
        $record = $this->makeServiceRecord($vehicle, [
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 12000,
        ]);

        $this->service->updateAfterService($vehicle, $record);

        $vehicle->refresh();
        $this->assertEquals('good', $vehicle->health_status);
    }

    /** @test */
    public function health_status_is_critical_when_oil_life_very_low()
    {
        $vehicle = $this->makeVehicle([
            'oil_life_percentage' => 5,  // Very low
            'next_service_odometer' => null,
        ]);

        $status = $this->service->calculateHealthStatus($vehicle, new ServiceRecord(['odometer_at_service' => 12000]), 5);

        $this->assertEquals('critical', $status);
    }

    /** @test */
    public function health_status_is_warning_when_oil_life_between_10_and_30()
    {
        $vehicle = $this->makeVehicle(['next_service_odometer' => null]);
        $status = $this->service->calculateHealthStatus($vehicle, new ServiceRecord(['odometer_at_service' => 12000]), 25);

        $this->assertEquals('warning', $status);
    }

    /** @test */
    public function health_status_is_good_when_oil_life_above_30()
    {
        $vehicle = $this->makeVehicle(['next_service_odometer' => null]);
        $status = $this->service->calculateHealthStatus($vehicle, new ServiceRecord(['odometer_at_service' => 12000]), 80);

        $this->assertEquals('good', $status);
    }

    // =========================================================
    // Test: updateAfterService — Odometer Update
    // =========================================================

    /** @test */
    public function vehicle_odometer_updated_when_service_odometer_higher()
    {
        $vehicle = $this->makeVehicle(['current_odometer' => 10000]);
        $record = $this->makeServiceRecord($vehicle, ['odometer_at_service' => 14000]);

        $this->service->updateAfterService($vehicle, $record);

        $vehicle->refresh();
        $this->assertEquals(14000, $vehicle->current_odometer);
    }

    /** @test */
    public function vehicle_odometer_not_decreased_when_service_odometer_lower()
    {
        $vehicle = $this->makeVehicle(['current_odometer' => 15000]);
        $record = $this->makeServiceRecord($vehicle, ['odometer_at_service' => 14000]);

        $this->service->updateAfterService($vehicle, $record);

        $vehicle->refresh();
        // Should stay at 15000, not decrease to 14000
        $this->assertEquals(15000, $vehicle->current_odometer);
    }

    /** @test */
    public function next_service_odometer_set_to_current_plus_interval()
    {
        $vehicle = $this->makeVehicle(['current_odometer' => 10000]);
        $record = $this->makeServiceRecord($vehicle, ['odometer_at_service' => 12000]);

        $this->service->updateAfterService($vehicle, $record);

        $vehicle->refresh();
        $this->assertEquals(12000 + VehicleHealthService::DEFAULT_SERVICE_ODOMETER_INTERVAL, $vehicle->next_service_odometer);
    }
}
