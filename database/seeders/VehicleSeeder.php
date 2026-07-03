<?php

namespace Database\Seeders;

use App\Models\QrCode;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VehicleSeeder extends Seeder
{
    /**
     * Seed sample vehicles.
     *
     * Creates 5 vehicles distributed among 3 vehicle owners.
     *   - Rian      : 2 vehicles (motor matic, motor sport)
     *   - Dewi      : 2 vehicles (mobil keluarga, motor matic)
     *   - Agus      : 1 vehicle  (motor bebek)
     *
     * Each vehicle also gets one active QrCode record.
     * Idempotent — uses firstOrCreate on plate_number.
     */
    public function run(): void
    {
        $rian = User::where('email', 'rian@maintify.app')->first();
        $dewi = User::where('email', 'dewi@maintify.app')->first();
        $agus = User::where('email', 'agus@maintify.app')->first();

        if (! $rian || ! $dewi || ! $agus) {
            $this->command->warn('⚠️  Vehicle owner users not found. Run UserSeeder first.');
            return;
        }

        $vehicles = [
            // ── Rian ──────────────────────────────────────────
            [
                'user_id'               => $rian->id,
                'plate_number'          => 'B 1234 RIA',
                'brand'                 => 'Honda',
                'model'                 => 'Vario 160',
                'type'                  => 'CBS ISS',
                'year'                  => 2022,
                'color'                 => 'Putih Pearl',
                'engine_number'         => 'JF84E1234567',
                'chassis_number'        => 'MH1JF8410NK123456',
                'current_odometer'      => 18500,
                'next_service_odometer' => 20000,
                'next_service_date'     => now()->addDays(14)->toDateString(),
                'health_status'         => 'good',
                'health_score'          => 85,
                'is_active'             => true,
            ],
            [
                'user_id'               => $rian->id,
                'plate_number'          => 'B 5678 RIB',
                'brand'                 => 'Yamaha',
                'model'                 => 'MT-15',
                'type'                  => 'Version S',
                'year'                  => 2021,
                'color'                 => 'Hitam Matte',
                'engine_number'         => '5D7E2234567',
                'chassis_number'        => 'MH35D7410MK234567',
                'current_odometer'      => 32000,
                'next_service_odometer' => 32500,
                'next_service_date'     => now()->subDays(5)->toDateString(), // sudah lewat → warning
                'health_status'         => 'warning',
                'health_score'          => 58,
                'is_active'             => true,
            ],
            // ── Dewi ──────────────────────────────────────────
            [
                'user_id'               => $dewi->id,
                'plate_number'          => 'B 2468 DEW',
                'brand'                 => 'Toyota',
                'model'                 => 'Avanza',
                'type'                  => 'G CVT',
                'year'                  => 2020,
                'color'                 => 'Silver Metallic',
                'engine_number'         => '2NRH123456',
                'chassis_number'        => 'MHFAH3BA0L0123456',
                'current_odometer'      => 55000,
                'next_service_odometer' => 60000,
                'next_service_date'     => now()->addDays(45)->toDateString(),
                'health_status'         => 'good',
                'health_score'          => 72,
                'is_active'             => true,
            ],
            [
                'user_id'               => $dewi->id,
                'plate_number'          => 'B 1357 DEX',
                'brand'                 => 'Honda',
                'model'                 => 'BeAT',
                'type'                  => 'Street ESP',
                'year'                  => 2019,
                'color'                 => 'Biru Darkspeed',
                'engine_number'         => 'eSP12345678',
                'chassis_number'        => 'MH1JF5110KK345678',
                'current_odometer'      => 41000,
                'next_service_odometer' => 41500,
                'next_service_date'     => now()->subDays(20)->toDateString(), // sudah lewat → critical
                'health_status'         => 'critical',
                'health_score'          => 32,
                'is_active'             => true,
            ],
            // ── Agus ──────────────────────────────────────────
            [
                'user_id'               => $agus->id,
                'plate_number'          => 'D 9876 AGS',
                'brand'                 => 'Honda',
                'model'                 => 'Supra X 125',
                'type'                  => 'CW FI',
                'year'                  => 2018,
                'color'                 => 'Merah',
                'engine_number'         => 'JB51E9876543',
                'chassis_number'        => 'MH1JB5110JK987654',
                'current_odometer'      => 67000,
                'next_service_odometer' => 70000,
                'next_service_date'     => now()->addDays(60)->toDateString(),
                'health_status'         => 'good',
                'health_score'          => 91,
                'is_active'             => true,
            ],
        ];

        foreach ($vehicles as $data) {
            $vehicle = Vehicle::firstOrCreate(
                ['plate_number' => $data['plate_number']],
                $data
            );

            // Buat QR Code aktif untuk kendaraan ini (jika belum ada)
            QrCode::firstOrCreate(
                ['vehicle_id' => $vehicle->id, 'status' => 'active'],
                [
                    'qr_token'  => strtoupper(Str::random(24)),
                    'status'    => 'active',
                    'issued_at' => now()->subDays(rand(30, 180)),
                ]
            );
        }

        $this->command->info('✅ Vehicles seeded (5 vehicles with active QR codes)');
    }
}
