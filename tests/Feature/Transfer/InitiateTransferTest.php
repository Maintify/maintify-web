<?php

namespace Tests\Feature\Transfer;

use App\Models\Notification;
use App\Models\OwnershipTransfer;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InitiateTransferTest extends TestCase
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

    /** @test */
    public function owner_can_view_transfer_initiation_form()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();

        $this->withoutVite();

        $response = $this->actingAs($owner)
            ->get(route('vehicles.transfer.create', $vehicle));

        $response->assertStatus(200);
        $response->assertSee('Transfer Kepemilikan');
        $response->assertSee($vehicle->brand);
        $response->assertSee($vehicle->model);
        $response->assertSee($vehicle->plate_number);
    }

    /** @test */
    public function owner_can_initiate_transfer_with_valid_recipient_email()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
            'email' => 'recipient@example.com',
        ]);

        $response = $this->actingAs($owner)
            ->post(route('vehicles.transfer.store', $vehicle), [
                'recipient_identifier' => 'recipient@example.com',
            ]);

        $response->assertRedirect(route('vehicles.show', $vehicle));
        $response->assertSessionHas('success');

        // Verify transfer record created
        $this->assertDatabaseHas('ownership_transfers', [
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
        ]);
    }

    /** @test */
    public function owner_can_initiate_transfer_with_valid_recipient_phone()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
            'phone_number' => '081234567890',
        ]);

        $response = $this->actingAs($owner)
            ->post(route('vehicles.transfer.store', $vehicle), [
                'recipient_identifier' => '081234567890',
            ]);

        $response->assertRedirect(route('vehicles.show', $vehicle));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ownership_transfers', [
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
        ]);
    }

    /** @test */
    public function cannot_transfer_to_non_existent_user()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();

        $response = $this->actingAs($owner)
            ->post(route('vehicles.transfer.store', $vehicle), [
                'recipient_identifier' => 'nonexistent@example.com',
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('recipient_identifier');

        // No transfer record should be created
        $this->assertDatabaseMissing('ownership_transfers', [
            'vehicle_id' => $vehicle->id,
        ]);
    }

    /** @test */
    public function cannot_transfer_to_self()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();

        $response = $this->actingAs($owner)
            ->post(route('vehicles.transfer.store', $vehicle), [
                'recipient_identifier' => $owner->email,
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors('recipient_identifier');

        $this->assertDatabaseMissing('ownership_transfers', [
            'vehicle_id' => $vehicle->id,
        ]);
    }

    /** @test */
    public function cannot_initiate_transfer_if_pending_transfer_exists()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        // Create an existing pending transfer
        OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            'requested_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        $newRecipient = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
            'email' => 'another@example.com',
        ]);

        // Try to initiate another transfer
        $response = $this->actingAs($owner)
            ->post(route('vehicles.transfer.store', $vehicle), [
                'recipient_identifier' => 'another@example.com',
            ]);

        $response->assertRedirect(route('vehicles.show', $vehicle));
        $response->assertSessionHas('error');

        // Only one transfer record should exist
        $this->assertEquals(1, OwnershipTransfer::where('vehicle_id', $vehicle->id)->count());
    }

    /** @test */
    public function non_owner_cannot_access_transfer_form()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $otherUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $response = $this->actingAs($otherUser)
            ->get(route('vehicles.transfer.create', $vehicle));

        $response->assertStatus(403);
    }

    /** @test */
    public function notification_is_created_for_recipient_upon_successful_initiation()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
            'email' => 'recipient@example.com',
        ]);

        $this->actingAs($owner)
            ->post(route('vehicles.transfer.store', $vehicle), [
                'recipient_identifier' => 'recipient@example.com',
            ]);

        // Verify notification was created for recipient
        $this->assertDatabaseHas('notifications', [
            'user_id' => $recipient->id,
            'type' => 'transfer_request',
            'is_read' => false,
        ]);
    }

    /** @test */
    public function transfer_expires_at_is_set_to_7_days_from_now()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
            'email' => 'recipient@example.com',
        ]);

        $this->actingAs($owner)
            ->post(route('vehicles.transfer.store', $vehicle), [
                'recipient_identifier' => 'recipient@example.com',
            ]);

        $transfer = OwnershipTransfer::where('vehicle_id', $vehicle->id)->first();

        $this->assertNotNull($transfer->expires_at);
        // expires_at should be approximately 7 days from now (within 1 minute tolerance)
        $this->assertTrue(
            $transfer->expires_at->diffInMinutes(now()->addDays(7)) < 1,
            'Transfer should expire in approximately 7 days.'
        );
    }

    /** @test */
    public function recipient_identifier_is_required()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();

        $response = $this->actingAs($owner)
            ->post(route('vehicles.transfer.store', $vehicle), [
                'recipient_identifier' => '',
            ]);

        $response->assertSessionHasErrors('recipient_identifier');
    }

    /** @test */
    public function cannot_view_transfer_form_if_pending_transfer_exists()
    {
        [$owner, $vehicle] = $this->createOwnerWithVehicle();
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            'requested_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->actingAs($owner)
            ->get(route('vehicles.transfer.create', $vehicle));

        $response->assertRedirect(route('vehicles.show', $vehicle));
        $response->assertSessionHas('error');
    }
}
