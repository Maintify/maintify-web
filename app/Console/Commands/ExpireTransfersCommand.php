<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\OwnershipTransfer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireTransfersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfers:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically expire pending transfer requests that are older than 7 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredTransfers = OwnershipTransfer::where('status', OwnershipTransfer::STATUS_PENDING_RECIPIENT)
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredTransfers->isEmpty()) {
            $this->info('No pending transfers to expire.');

            return self::SUCCESS;
        }

        $count = 0;

        foreach ($expiredTransfers as $transfer) {
            DB::transaction(function () use ($transfer, &$count) {
                $transfer->update([
                    'status' => OwnershipTransfer::STATUS_EXPIRED,
                ]);

                // Notify sender
                Notification::create([
                    'user_id' => $transfer->from_user_id,
                    'type' => 'transfer_expired',
                    'title' => 'Permintaan Transfer Kedaluwarsa',
                    'message' => 'Permintaan transfer kendaraan '.($transfer->vehicle->plate_number ?? '').' ke '.($transfer->toUser->name ?? '').' telah kedaluwarsa karena tidak ada respons.',
                    'is_read' => false,
                ]);

                // Notify recipient
                Notification::create([
                    'user_id' => $transfer->to_user_id,
                    'type' => 'transfer_expired',
                    'title' => 'Permintaan Transfer Kedaluwarsa',
                    'message' => 'Permintaan transfer kendaraan '.($transfer->vehicle->plate_number ?? '').' dari '.($transfer->fromUser->name ?? '').' telah kedaluwarsa.',
                    'is_read' => false,
                ]);

                AuditLog::record(
                    'transfer_auto_expired',
                    'OwnershipTransfer',
                    $transfer->id,
                    [
                        'vehicle_id' => $transfer->vehicle_id,
                        'from_user_id' => $transfer->from_user_id,
                        'to_user_id' => $transfer->to_user_id,
                    ]
                );

                $count++;
            });
        }

        $this->info("Successfully expired {$count} transfer(s).");

        return self::SUCCESS;
    }
}
