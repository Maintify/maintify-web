<?php

namespace App\Services;

use App\Models\ServiceRecord;
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
     */
    public function updateAfterService(Vehicle $vehicle, ServiceRecord $record): void
    {
        $updates = [];

        // 1. Update oil_life_percentage jika ini adalah ganti oli
        if ($record->service_type === ServiceRecord::TYPE_OIL_CHANGE) {
            $updates['oil_life_percentage'] = 100;
        } else {
            // Kurangi oil life berdasarkan km yang ditempuh sejak last service
            $updates['oil_life_percentage'] = $this->calculateOilLife($vehicle, $record);
        }

        // 2. Update current_odometer jika odometer service lebih besar dari saat ini
        if ($record->odometer_at_service > $vehicle->current_odometer) {
            $updates['current_odometer'] = $record->odometer_at_service;
        }

        // 3. Kalkulasi health_status berdasarkan aturan bisnis
        $updates['health_status'] = $this->calculateHealthStatus($vehicle, $record, $updates['oil_life_percentage'] ?? $vehicle->oil_life_percentage);

        // 4. Update next_service_odometer
        if ($record->odometer_at_service) {
            $updates['next_service_odometer'] = $record->odometer_at_service + self::DEFAULT_SERVICE_ODOMETER_INTERVAL;
        }

        // 5. Update next_service_date
        $updates['next_service_date'] = now()->addDays(self::DEFAULT_SERVICE_DAY_INTERVAL)->toDateString();

        $vehicle->update($updates);
    }

    /**
     * Hitung oil_life_percentage sisa berdasarkan km yang ditempuh sejak service terakhir.
     *
     * Kalkulasi sederhana:
     * - Ambil odometer ganti oli terakhir
     * - Hitung selisih dengan odometer service sekarang
     * - Kurangi dari 100% berdasarkan persentase interval
     *
     * @return int 0–100
     */
    public function calculateOilLife(Vehicle $vehicle, ServiceRecord $record): int
    {
        // Cari ganti oli terakhir
        $lastOilChange = $vehicle->serviceRecords()
            ->where('service_type', ServiceRecord::TYPE_OIL_CHANGE)
            ->where('status', ServiceRecord::STATUS_COMPLETED)
            ->latest('service_date')
            ->first();

        if (! $lastOilChange || ! $lastOilChange->odometer_at_service) {
            // Tidak ada data ganti oli sebelumnya — jaga nilai saat ini
            return max(0, $vehicle->oil_life_percentage ?? 100);
        }

        $kmSinceOilChange = $record->odometer_at_service - $lastOilChange->odometer_at_service;

        if ($kmSinceOilChange <= 0) {
            return $vehicle->oil_life_percentage ?? 100;
        }

        $percentageUsed = ($kmSinceOilChange / self::DEFAULT_SERVICE_ODOMETER_INTERVAL) * 100;
        $remaining = 100 - (int) $percentageUsed;

        return max(0, min(100, $remaining));
    }

    /**
     * Hitung health_status kendaraan.
     *
     * Rules:
     * - 'critical' → oil_life <= 10% ATAU odometer melebihi interval service
     * - 'warning'  → oil_life <= 30% ATAU odometer mendekati interval (80% tercapai)
     * - 'good'     → kondisi normal
     *
     * @param  int  $newOilLife  Oil life yang baru dikalkulasi
     * @return string 'good'|'warning'|'critical'
     */
    public function calculateHealthStatus(Vehicle $vehicle, ServiceRecord $record, int $newOilLife): string
    {
        // Cek oil life
        if ($newOilLife <= 10) {
            return 'critical';
        }

        if ($newOilLife <= 30) {
            return 'warning';
        }

        // Cek odometer vs next_service_odometer (jika ada)
        if ($vehicle->next_service_odometer && $record->odometer_at_service) {
            $odometerGap = $vehicle->next_service_odometer - $record->odometer_at_service;
            $totalInterval = self::DEFAULT_SERVICE_ODOMETER_INTERVAL;

            if ($odometerGap <= 0) {
                return 'critical';
            }

            $percentageRemaining = $odometerGap / $totalInterval;

            if ($percentageRemaining <= self::WARNING_THRESHOLD_PERCENT) {
                return 'warning';
            }
        }

        // Cek waktu sejak service terakhir
        $lastService = $vehicle->serviceRecords()
            ->where('status', ServiceRecord::STATUS_COMPLETED)
            ->latest('service_date')
            ->first();

        if ($lastService && $lastService->service_date) {
            $daysSince = (int) $lastService->service_date->diffInDays(now());

            if ($daysSince >= self::DEFAULT_SERVICE_DAY_INTERVAL) {
                return 'warning';
            }
        }

        return 'good';
    }
}
