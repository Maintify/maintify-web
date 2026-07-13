<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\OwnershipTransfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class OwnershipTransferService
{
    /**
     * Approve a pending transfer by the recipient.
     */
    public function approve(OwnershipTransfer $transfer, User $recipient): void
    {
        if ($transfer->to_user_id !== $recipient->id) {
            throw new Exception('Anda tidak berhak menyetujui transfer ini.');
        }

        if ($transfer->status !== OwnershipTransfer::STATUS_PENDING_RECIPIENT) {
            throw new Exception('Status transfer tidak valid untuk disetujui.');
        }

        DB::transaction(function () use ($transfer, $recipient) {
            $transfer->update([
                'status' => OwnershipTransfer::STATUS_APPROVED,
                'approved_at' => now(),
            ]);

            // Notify original owner
            Notification::create([
                'user_id' => $transfer->from_user_id,
                'type' => 'transfer_approved',
                'title' => 'Permintaan Transfer Disetujui',
                'message' => $recipient->name.' telah menyetujui permintaan transfer kendaraan Anda. Silakan selesaikan proses konfirmasi.',
                'is_read' => false,
            ]);

            AuditLog::record(
                'transfer_approved_by_recipient',
                'OwnershipTransfer',
                $transfer->id,
                [
                    'vehicle_id' => $transfer->vehicle_id,
                    'to_user_id' => $recipient->id,
                ]
            );
        });
    }

    /**
     * Reject a pending transfer by the recipient.
     */
    public function reject(OwnershipTransfer $transfer, User $recipient): void
    {
        if ($transfer->to_user_id !== $recipient->id) {
            throw new Exception('Anda tidak berhak menolak transfer ini.');
        }

        if ($transfer->status !== OwnershipTransfer::STATUS_PENDING_RECIPIENT) {
            throw new Exception('Status transfer tidak valid untuk ditolak.');
        }

        DB::transaction(function () use ($transfer, $recipient) {
            $transfer->update([
                'status' => OwnershipTransfer::STATUS_REJECTED,
            ]);

            // Notify original owner
            Notification::create([
                'user_id' => $transfer->from_user_id,
                'type' => 'transfer_rejected',
                'title' => 'Permintaan Transfer Ditolak',
                'message' => $recipient->name.' menolak permintaan transfer kendaraan Anda.',
                'is_read' => false,
            ]);

            AuditLog::record(
                'transfer_rejected_by_recipient',
                'OwnershipTransfer',
                $transfer->id,
                [
                    'vehicle_id' => $transfer->vehicle_id,
                    'to_user_id' => $recipient->id,
                ]
            );
        });
    }

    /**
     * Final confirmation by the original owner, executing the transfer atomically.
     */
    public function confirm(OwnershipTransfer $transfer, User $owner, string $disclaimerText): void
    {
        if ($transfer->from_user_id !== $owner->id) {
            throw new Exception('Anda tidak berhak mengonfirmasi transfer ini.');
        }

        if ($transfer->status !== OwnershipTransfer::STATUS_APPROVED) {
            throw new Exception('Transfer harus disetujui penerima terlebih dahulu sebelum dapat dikonfirmasi.');
        }

        $vehicle = $transfer->vehicle;
        if (! $vehicle) {
            throw new Exception('Kendaraan tidak ditemukan.');
        }

        DB::transaction(function () use ($transfer, $vehicle, $disclaimerText) {
            // 1. Update transfer record
            $transfer->update([
                'status' => OwnershipTransfer::STATUS_CONFIRMED,
                'disclaimer_acknowledged' => $disclaimerText,
                'confirmed_at' => now(),
            ]);

            // 2. Transfer vehicle ownership
            $vehicle->update([
                'user_id' => $transfer->to_user_id,
            ]);

            // 3. Notify new owner
            Notification::create([
                'user_id' => $transfer->to_user_id,
                'type' => 'transfer_completed',
                'title' => 'Transfer Kendaraan Berhasil',
                'message' => 'Kendaraan '.$vehicle->brand.' '.$vehicle->model.' ('.$vehicle->plate_number.') kini resmi menjadi milik Anda.',
                'is_read' => false,
            ]);

            // 4. Audit Log
            AuditLog::record(
                'transfer_completed_confirmed',
                'OwnershipTransfer',
                $transfer->id,
                [
                    'vehicle_id' => $vehicle->id,
                    'from_user_id' => $transfer->from_user_id,
                    'to_user_id' => $transfer->to_user_id,
                    'disclaimer' => $disclaimerText,
                ]
            );
        });
    }
}
