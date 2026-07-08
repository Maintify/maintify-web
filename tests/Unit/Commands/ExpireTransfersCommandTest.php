<?php

namespace Tests\Unit\Commands;

use App\Models\OwnershipTransfer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ExpireTransfersCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_expires_transfers_older_than_7_days_and_sends_notifications()
    {
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1234 XYZ',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'year' => 2023,
            'color' => 'Silver',
            'current_odometer' => 15000,
            'is_active' => true,
        ]);

        // Create an expired transfer
        $expiredTransfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            'requested_at' => now()->subDays(10),
            'expires_at' => now()->subDays(3),
        ]);

        // Create a non-expired transfer
        $validTransfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            'requested_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        Artisan::call('transfers:expire');

        // Check if expired
        $this->assertEquals(OwnershipTransfer::STATUS_EXPIRED, $expiredTransfer->fresh()->status);
        $this->assertEquals(OwnershipTransfer::STATUS_PENDING_RECIPIENT, $validTransfer->fresh()->status);

        // Check notifications
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'transfer_expired',
        ]);
        
        $this->assertDatabaseHas('notifications', [
            'user_id' => $recipient->id,
            'type' => 'transfer_expired',
        ]);

        // Check audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'transfer_auto_expired',
            'entity_type' => 'OwnershipTransfer',
            'entity_id' => $expiredTransfer->id,
        ]);
    }
}
