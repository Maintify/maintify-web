<?php

namespace Tests\Feature\Transfer;

use App\Models\Notification;
use App\Models\OwnershipTransfer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function createOwnerWithVehicle(): array
    {
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

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

        return [$owner, $vehicle];
    }

    private function createPendingTransfer(Vehicle $vehicle, User $owner, User $recipient): OwnershipTransfer
    {
        return OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            'requested_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);
    }

    /** @test */
    public function recipient_can_approve_transfer_request()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $transfer = $this->createPendingTransfer($vehicle, $owner, $recipient);

        $response = $this->actingAs($recipient)
            ->post(route('transfers.approve', $transfer));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(OwnershipTransfer::STATUS_APPROVED, $transfer->fresh()->status);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'transfer_approved',
        ]);
    }

    /** @test */
    public function recipient_can_reject_transfer_request()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $transfer = $this->createPendingTransfer($vehicle, $owner, $recipient);

        $response = $this->actingAs($recipient)
            ->post(route('transfers.reject', $transfer));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(OwnershipTransfer::STATUS_REJECTED, $transfer->fresh()->status);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'transfer_rejected',
        ]);
    }

    /** @test */
    public function owner_can_view_review_page_of_approved_transfer()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $transfer = $this->createPendingTransfer($vehicle, $owner, $recipient);
        $transfer->update(['status' => OwnershipTransfer::STATUS_APPROVED]);

        $this->withoutVite();

        $response = $this->actingAs($owner)
            ->get(route('transfers.review', $transfer));

        $response->assertStatus(200);
        $response->assertSee('Konfirmasi Akhir');
        $response->assertSee($recipient->name);
    }

    /** @test */
    public function owner_cannot_view_review_page_if_not_approved()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $transfer = $this->createPendingTransfer($vehicle, $owner, $recipient); // pending

        $response = $this->actingAs($owner)
            ->get(route('transfers.review', $transfer));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function owner_can_confirm_approved_transfer()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $transfer = $this->createPendingTransfer($vehicle, $owner, $recipient);
        $transfer->update(['status' => OwnershipTransfer::STATUS_APPROVED]);

        $response = $this->actingAs($owner)
            ->post(route('transfers.confirm', $transfer), [
                'disclaimer_agreed' => '1',
            ]);

        $response->assertRedirect(route('transfers.success', $transfer));

        // Assert ownership changed
        $this->assertEquals($recipient->id, $vehicle->fresh()->user_id);

        // Assert transfer status
        $this->assertEquals(OwnershipTransfer::STATUS_CONFIRMED, $transfer->fresh()->status);
        $this->assertNotNull($transfer->fresh()->disclaimer_acknowledged);

        // Assert notification for new owner
        $this->assertDatabaseHas('notifications', [
            'user_id' => $recipient->id,
            'type' => 'transfer_completed',
        ]);

        // Assert audit log exists
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'transfer_completed_confirmed',
            'entity_type' => 'OwnershipTransfer',
            'entity_id' => $transfer->id,
        ]);
    }

    /** @test */
    public function owner_cannot_confirm_transfer_without_acknowledging_disclaimer()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $transfer = $this->createPendingTransfer($vehicle, $owner, $recipient);
        $transfer->update(['status' => OwnershipTransfer::STATUS_APPROVED]);

        $response = $this->actingAs($owner)
            ->post(route('transfers.confirm', $transfer)); // Missing disclaimer_agreed

        $response->assertSessionHasErrors('disclaimer_agreed');

        // Assert ownership did not change
        $this->assertEquals($owner->id, $vehicle->fresh()->user_id);
    }

    /** @test */
    public function non_recipient_cannot_approve_or_reject()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $otherUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $transfer = $this->createPendingTransfer($vehicle, $owner, $recipient);

        $response = $this->actingAs($otherUser)
            ->post(route('transfers.approve', $transfer));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(OwnershipTransfer::STATUS_PENDING_RECIPIENT, $transfer->fresh()->status);
    }

    /** @test */
    public function non_owner_cannot_confirm()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $otherUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $transfer = $this->createPendingTransfer($vehicle, $owner, $recipient);
        $transfer->update(['status' => OwnershipTransfer::STATUS_APPROVED]);

        $response = $this->actingAs($otherUser)
            ->post(route('transfers.confirm', $transfer), [
                'disclaimer_agreed' => '1',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals($owner->id, $vehicle->fresh()->user_id);
    }
}
