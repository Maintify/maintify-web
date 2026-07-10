<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\ServiceRecord;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Services\VehicleHealthService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function super_admin_can_view_global_settings_page()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($superAdmin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Interval Pengingat Servis (Hari)');
        $response->assertSee('Interval Pengingat Servis (Odometer - Km)');
        $response->assertSee('Masa Kedaluwarsa Transfer Kepemilikan (Hari)');
    }

    /** @test */
    public function super_admin_can_update_global_settings()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // Default setting seeds are implicitly tested via defaults in the controller
        $response = $this->actingAs($superAdmin)->put(route('admin.settings.update'), [
            'service_reminder_interval' => 90,
            'service_reminder_mileage' => 8000,
            'transfer_expiry_days' => 15,
        ]);

        $response->assertRedirect();
        
        $this->assertEquals('90', Setting::get('service_reminder_interval'));
        $this->assertEquals('8000', Setting::get('service_reminder_mileage'));
        $this->assertEquals('15', Setting::get('transfer_expiry_days'));

        // Assert audit log created
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'global_settings_update',
            'entity_type' => 'settings',
        ]);
    }

    /** @test */
    public function settings_validation_rules_prevent_invalid_values()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $response = $this->actingAs($superAdmin)->put(route('admin.settings.update'), [
            'service_reminder_interval' => -10, // Negative
            'service_reminder_mileage' => 0, // Zero
            'transfer_expiry_days' => '', // Empty
        ]);

        $response->assertSessionHasErrors([
            'service_reminder_interval',
            'service_reminder_mileage',
            'transfer_expiry_days',
        ]);
    }

    /** @test */
    public function non_super_admin_cannot_access_or_update_global_settings()
    {
        $regularUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $this->actingAs($regularUser)->get(route('admin.settings.index'))->assertStatus(403);

        $this->actingAs($regularUser)->put(route('admin.settings.update'), [
            'service_reminder_interval' => 90,
            'service_reminder_mileage' => 8000,
            'transfer_expiry_days' => 15,
        ])->assertStatus(403);
    }

    /** @test */
    public function changed_settings_take_effect_in_vehicle_service_logic()
    {
        // 1. Set custom settings
        Setting::set('service_reminder_interval', 60);
        Setting::set('service_reminder_mileage', 7500);

        // 2. Create vehicle and service record
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario',
            'year' => 2020,
            'current_odometer' => 1000,
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
            'service_type' => 'tune_up',
            'service_date' => now(),
            'odometer_at_service' => 1500,
            'total_cost' => 150000,
        ]);

        // 3. Process calculations
        $service = new VehicleHealthService();
        $service->updateAfterService($vehicle, $serviceRecord);

        // 4. Assert vehicle values updated based on settings
        $vehicle->refresh();
        $this->assertEquals(1500 + 7500, $vehicle->next_service_odometer);
        
        $expectedDate = now()->addDays(60)->toDateString();
        $this->assertEquals($expectedDate, $vehicle->next_service_date->toDateString());
    }

    /** @test */
    public function changed_settings_take_effect_in_ownership_transfer_logic()
    {
        // 1. Set custom transfer expiry setting
        Setting::set('transfer_expiry_days', 12);

        // 2. Create users and vehicle
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 ABC',
            'brand' => 'Honda',
            'model' => 'Vario',
            'year' => 2020,
            'is_active' => true,
        ]);

        // 3. Request transfer via OwnershipTransferController endpoint
        $response = $this->actingAs($owner)->post(route('vehicles.transfer.store', $vehicle->id), [
            'recipient_identifier' => $recipient->email,
        ]);

        $response->assertRedirect();

        // 4. Assert transfer record has the correct expires_at
        $this->assertDatabaseHas('ownership_transfers', [
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
        ]);

        $transfer = \App\Models\OwnershipTransfer::where('vehicle_id', $vehicle->id)->latest()->first();
        $this->assertNotNull($transfer);

        $expectedExpiry = Carbon::now()->addDays(12)->toDateString();
        $this->assertEquals($expectedExpiry, $transfer->expires_at->toDateString());
    }
}
