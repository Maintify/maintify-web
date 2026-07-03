<?php

namespace Tests\Feature;

use App\Models\OwnershipTransfer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnershipTransferTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_ownership_transfer_with_correct_relations()
    {
        // 1. Create dependencies
        $fromUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $toUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle = Vehicle::create([
            'user_id' => $fromUser->id,
            'plate_number' => 'B 1234 XYZ',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'year' => 2023,
            'color' => 'Silver',
            'current_odometer' => 15000,
            'is_active' => true,
        ]);

        // 2. Create OwnershipTransfer
        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            'disclaimer_acknowledged' => 'Saya menyetujui transfer kepemilikan.',
            'requested_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        // 3. Assertions — database
        $this->assertDatabaseHas('ownership_transfers', [
            'id' => $transfer->id,
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $fromUser->id,
            'to_user_id' => $toUser->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
        ]);

        // 4. Assertions — model relationships
        $this->assertEquals($vehicle->id, $transfer->vehicle->id);
        $this->assertEquals($fromUser->id, $transfer->fromUser->id);
        $this->assertEquals($toUser->id, $transfer->toUser->id);

        // 5. Assertions — inverse relationships
        $this->assertTrue($vehicle->ownershipTransfers->contains($transfer));
        $this->assertTrue($fromUser->outgoingTransfers->contains($transfer));
        $this->assertTrue($toUser->incomingTransfers->contains($transfer));
    }

    /** @test */
    public function it_supports_all_status_enum_values()
    {
        $fromUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $toUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $vehicle = Vehicle::create([
            'user_id' => $fromUser->id,
            'plate_number' => 'D 5678 ABC',
            'brand' => 'Honda',
            'model' => 'Jazz',
            'year' => 2022,
            'color' => 'White',
            'current_odometer' => 30000,
            'is_active' => true,
        ]);

        $statuses = [
            OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            OwnershipTransfer::STATUS_APPROVED,
            OwnershipTransfer::STATUS_CONFIRMED,
            OwnershipTransfer::STATUS_REJECTED,
            OwnershipTransfer::STATUS_EXPIRED,
        ];

        foreach ($statuses as $status) {
            $transfer = OwnershipTransfer::create([
                'vehicle_id' => $vehicle->id,
                'from_user_id' => $fromUser->id,
                'to_user_id' => $toUser->id,
                'status' => $status,
                'requested_at' => now(),
            ]);

            $this->assertDatabaseHas('ownership_transfers', [
                'id' => $transfer->id,
                'status' => $status,
            ]);
        }
    }
}
