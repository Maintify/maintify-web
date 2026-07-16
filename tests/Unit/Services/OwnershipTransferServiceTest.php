<?php

namespace Tests\Unit\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\OwnershipTransfer;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\OwnershipTransferService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnershipTransferServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OwnershipTransferService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OwnershipTransferService;
    }

    // =========================================================
    // Approve Tests
    // =========================================================

    public function test_approve_successfully_updates_status_and_logs(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $recipient = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
        ]);

        $this->actingAs($recipient);

        $this->service->approve($transfer, $recipient);

        $transfer->refresh();

        $this->assertEquals(OwnershipTransfer::STATUS_APPROVED, $transfer->status);
        $this->assertNotNull($transfer->approved_at);

        // Assert notification created for from_user
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'transfer_approved',
        ]);

        // Assert audit log created
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $recipient->id,
            'action' => 'transfer_approved_by_recipient',
            'entity_type' => 'OwnershipTransfer',
            'entity_id' => $transfer->id,
        ]);
    }

    public function test_approve_throws_exception_if_user_is_not_recipient(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Anda tidak berhak menyetujui transfer ini.');

        $this->service->approve($transfer, $otherUser);
    }

    public function test_approve_throws_exception_if_status_is_not_pending_recipient(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_APPROVED,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Status transfer tidak valid untuk disetujui.');

        $this->service->approve($transfer, $recipient);
    }

    // =========================================================
    // Reject Tests
    // =========================================================

    public function test_reject_successfully_updates_status_and_logs(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
        ]);

        $this->actingAs($recipient);

        $this->service->reject($transfer, $recipient);

        $transfer->refresh();

        $this->assertEquals(OwnershipTransfer::STATUS_REJECTED, $transfer->status);

        // Assert notification created for from_user
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'transfer_rejected',
        ]);

        // Assert audit log created
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $recipient->id,
            'action' => 'transfer_rejected_by_recipient',
            'entity_type' => 'OwnershipTransfer',
            'entity_id' => $transfer->id,
        ]);
    }

    public function test_reject_throws_exception_if_user_is_not_recipient(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Anda tidak berhak menolak transfer ini.');

        $this->service->reject($transfer, $otherUser);
    }

    public function test_reject_throws_exception_if_status_is_not_pending_recipient(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_CONFIRMED,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Status transfer tidak valid untuk ditolak.');

        $this->service->reject($transfer, $recipient);
    }

    // =========================================================
    // Confirm Tests
    // =========================================================

    public function test_confirm_successfully_transfers_vehicle_and_logs(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_APPROVED,
        ]);

        $this->actingAs($owner);

        $disclaimer = 'Saya setuju memindahkan kepemilikan';
        $this->service->confirm($transfer, $owner, $disclaimer);

        $transfer->refresh();
        $vehicle->refresh();

        $this->assertEquals(OwnershipTransfer::STATUS_CONFIRMED, $transfer->status);
        $this->assertEquals($disclaimer, $transfer->disclaimer_acknowledged);
        $this->assertNotNull($transfer->confirmed_at);

        // Vehicle owner must be recipient now
        $this->assertEquals($recipient->id, $vehicle->user_id);

        // Assert notification created for new owner (recipient)
        $this->assertDatabaseHas('notifications', [
            'user_id' => $recipient->id,
            'type' => 'transfer_completed',
        ]);

        // Assert audit log created
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $owner->id,
            'action' => 'transfer_completed_confirmed',
            'entity_type' => 'OwnershipTransfer',
            'entity_id' => $transfer->id,
        ]);
    }

    public function test_confirm_throws_exception_if_user_is_not_original_owner(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $otherUser = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_APPROVED,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Anda tidak berhak mengonfirmasi transfer ini.');

        $this->service->confirm($transfer, $otherUser, 'Disclaimer');
    }

    public function test_confirm_throws_exception_if_status_is_not_approved(): void
    {
        $owner = User::factory()->create();
        $recipient = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Transfer harus disetujui penerima terlebih dahulu sebelum dapat dikonfirmasi.');

        $this->service->confirm($transfer, $owner, 'Disclaimer');
    }
}
