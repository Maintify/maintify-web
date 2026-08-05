<?php

namespace App\Services;

use App\Models\ServiceRecord;
use App\Models\Setting;
use App\Models\Vehicle;

/**
 * VehicleHealthService
 *
 * Mengelola logika kalkulasi otomatis health_status dan oil_life_percentage
 * kendaraan setelah setiap service record disimpan.
 *
 * Business Rules (Task 5.2.1a / Subtask 5.2.1a):
 * - Oil change resets oil_life to 100%
 * - Health status dikalkulasi berdasarkan waktu dan jarak odometer sejak service terakhir
 */
class VehicleHealthService
{
    /**
     * Interval odometer default (km) sebelum dianggap perlu service.
     */
    const DEFAULT_SERVICE_ODOMETER_INTERVAL = 5000;

    /**
     * Interval waktu default (hari) sebelum dianggap perlu service.
     */
    const DEFAULT_SERVICE_DAY_INTERVAL = 180; // 6 bulan

    /**
     * Threshold odometer (km) sebelum dianggap "warning" (persentase dari interval).
     * Jika sisa interval <= 20% → warning
     */
    const WARNING_THRESHOLD_PERCENT = 0.20;

    /**
     * Update vehicle health stats setelah service record baru disimpan.
     * Dipanggil setelah ServiceRecord berhasil dibuat.
     *
     * @param  Vehicle  $vehicle  Kendaraan yang di-service
     * @param  ServiceRecord  $record  Service record yang baru dibuat
     * @param  int|null  $nextServiceOdometer  Odometer target servis berikutnya (input mekanik)
     * @param  string|null  $nextServiceDate  Tanggal target servis berikutnya (input mekanik)
     */
    public function updateAfterService(Vehicle $vehicle, ServiceRecord $record, ?int $nextServiceOdometer = null, ?string $nextServiceDate = null): void
    {
        $updates = [];

        // Update current_odometer jika odometer service lebih besar dari saat ini
        if ($record->odometer_at_service > $vehicle->current_odometer) {
            $updates['current_odometer'] = $record->odometer_at_service;
        }

        // Update next_service_odometer (input manual mekanik dengan fallback otomatis)
        if ($nextServiceOdometer !== null) {
            $updates['next_service_odometer'] = $nextServiceOdometer;
        } elseif ($record->odometer_at_service) {
            $odometerInterval = (int) Setting::get('service_reminder_mileage', self::DEFAULT_SERVICE_ODOMETER_INTERVAL);
            $updates['next_service_odometer'] = $record->odometer_at_service + $odometerInterval;
        }

        // Update next_service_date (input manual mekanik dengan fallback otomatis)
        if ($nextServiceDate !== null) {
            $updates['next_service_date'] = $nextServiceDate;
        } else {
            $dayInterval = (int) Setting::get('service_reminder_interval', self::DEFAULT_SERVICE_DAY_INTERVAL);
            $updates['next_service_date'] = now()->addDays($dayInterval)->toDateString();
        }

        $vehicle->update($updates);
    }
}
