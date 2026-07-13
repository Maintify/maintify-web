<?php

namespace App\Console\Commands;

use App\Services\ServiceReminderService;
use Illuminate\Console\Command;

class SendServiceRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintify:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi pengingat servis berdasarkan batas waktu dan odometer kendaraan.';

    /**
     * Execute the console command.
     */
    public function handle(ServiceReminderService $serviceReminderService): int
    {
        $this->info('Memulai pemeriksaan dan pengiriman pengingat servis berkala...');

        $result = $serviceReminderService->sendServiceReminders();

        $this->info("Proses selesai. Terkirim: {$result['time_reminders_sent']} pengingat batas waktu, {$result['mileage_reminders_sent']} pengingat batas odometer.");

        return self::SUCCESS;
    }
}
