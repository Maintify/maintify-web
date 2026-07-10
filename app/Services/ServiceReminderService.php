<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\Vehicle;
use Carbon\Carbon;

class ServiceReminderService
{
    /**
     * Jalankan pemeriksaan dan kirim pengingat servis untuk semua kendaraan aktif.
     *
     * @return array Ringkasan jumlah notifikasi yang dikirim
     */
    public function sendServiceReminders(): array
    {
        $vehicles = Vehicle::where('is_active', true)->get();
        $timeSent = 0;
        $mileageSent = 0;

        $serviceReminderInterval = (int) Setting::get('service_reminder_interval', 180);
        $serviceReminderMileage = (int) Setting::get('service_reminder_mileage', 5000);

        // Odometer warning threshold: 20% dari interval odometer, atau minimal 1000 km
        $odometerThreshold = (int) max(1000, 0.20 * $serviceReminderMileage);

        foreach ($vehicles as $vehicle) {
            // 1. Pengingat Berdasarkan Batas Waktu (Time-based reminder)
            if ($vehicle->next_service_date && $vehicle->owner->enable_service_reminders) {
                $nextServiceDate = Carbon::parse($vehicle->next_service_date);
                
                if ($nextServiceDate->isPast() || $nextServiceDate->isToday()) {
                    // Cari apakah notifikasi untuk target tanggal servis ini sudah dikirim sebelumnya
                    $alreadySentTime = Notification::where('user_id', $vehicle->user_id)
                        ->where('type', 'service_reminder')
                        ->where('message', 'like', "%{$vehicle->plate_number}%")
                        ->where('message', 'like', "%{$nextServiceDate->format('d-m-Y')}%")
                        ->exists();

                    if (!$alreadySentTime) {
                        Notification::create([
                            'user_id' => $vehicle->user_id,
                            'type' => 'service_reminder',
                            'title' => 'Jadwal Servis Berkala Lewat Batas',
                            'message' => "Kendaraan {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}) sudah melewati atau mencapai batas waktu servis berkala pada {$nextServiceDate->format('d-m-Y')}.",
                            'is_read' => false,
                        ]);
                        $timeSent++;
                    }
                }
            }

            // 2. Pengingat Berdasarkan Batas Jarak Tempuh (Mileage-based reminder)
            if ($vehicle->next_service_odometer && $vehicle->current_odometer !== null && $vehicle->owner->enable_service_reminders) {
                $odometerGap = $vehicle->next_service_odometer - $vehicle->current_odometer;

                if ($odometerGap <= $odometerThreshold) {
                    // Cari apakah notifikasi untuk target odometer ini sudah dikirim sebelumnya
                    $alreadySentMileage = Notification::where('user_id', $vehicle->user_id)
                        ->where('type', 'service_reminder')
                        ->where('message', 'like', "%{$vehicle->plate_number}%")
                        ->where('message', 'like', "%Target Odometer: {$vehicle->next_service_odometer} km%")
                        ->exists();

                    if (!$alreadySentMileage) {
                        Notification::create([
                            'user_id' => $vehicle->user_id,
                            'type' => 'service_reminder',
                            'title' => 'Mendekati Batas Odometer Servis',
                            'message' => "Odometer kendaraan {$vehicle->brand} {$vehicle->model} ({$vehicle->plate_number}) saat ini ({$vehicle->current_odometer} km) mendekati atau melewati batas servis. Target Odometer: {$vehicle->next_service_odometer} km.",
                            'is_read' => false,
                        ]);
                        $mileageSent++;
                    }
                }
            }
        }

        return [
            'time_reminders_sent' => $timeSent,
            'mileage_reminders_sent' => $mileageSent,
        ];
    }
}
