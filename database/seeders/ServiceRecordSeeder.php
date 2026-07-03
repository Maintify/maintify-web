<?php

namespace Database\Seeders;

use App\Models\ServicePart;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Database\Seeder;

class ServiceRecordSeeder extends Seeder
{
    /**
     * Seed sample service records.
     *
     * Creates at least 10 service records spread across vehicles and
     * the approved workshop. Also seeds service_parts for each record.
     *
     * Idempotent — checks by (vehicle_id, workshop_id, service_date, odometer)
     * combination before inserting.
     */
    public function run(): void
    {
        $workshop = Workshop::where('status', Workshop::STATUS_APPROVED)->first();
        $workshopUser = $workshop?->user;

        if (! $workshop || ! $workshopUser) {
            $this->command->warn('⚠️  Approved workshop not found. Run WorkshopSeeder first.');
            return;
        }

        $vehicles = Vehicle::all()->keyBy('plate_number');

        if ($vehicles->isEmpty()) {
            $this->command->warn('⚠️  No vehicles found. Run VehicleSeeder first.');
            return;
        }

        // ─────────────────────────────────────────────────────────────────
        // Define service records
        // Each entry: [plate_number, service_type, date_offset_days, odometer, cost, notes, parts[]]
        // ─────────────────────────────────────────────────────────────────
        $records = [

            // ── Honda Vario 160 (Rian) ────────────────────────────────
            [
                'plate'        => 'B 1234 RIA',
                'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
                'days_ago'     => 90,
                'odometer'     => 12000,
                'cost'         => 95000,
                'notes'        => 'Ganti oli Motul 10W-40, kondisi mesin baik.',
                'parts'        => [
                    ['part_name' => 'Oli Motul 5100 10W-40 800ml', 'quantity' => 1, 'unit_price' => 75000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Busi NGK CPR9EA-9', 'quantity' => 1, 'unit_price' => 20000, 'part_category' => 'Pengapian'],
                ],
            ],
            [
                'plate'        => 'B 1234 RIA',
                'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
                'days_ago'     => 45,
                'odometer'     => 16000,
                'cost'         => 350000,
                'notes'        => 'Servis berkala 16.000 km: ganti oli, filter udara, setel klep.',
                'parts'        => [
                    ['part_name' => 'Oli AHM Oil MPX2 800ml', 'quantity' => 1, 'unit_price' => 55000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Filter Udara Honda Original', 'quantity' => 1, 'unit_price' => 85000, 'part_category' => 'Filter'],
                    ['part_name' => 'Busi NGK Original', 'quantity' => 1, 'unit_price' => 28000, 'part_category' => 'Pengapian'],
                    ['part_name' => 'Ongkos Setel Klep', 'quantity' => 1, 'unit_price' => 50000, 'part_category' => 'Jasa'],
                ],
            ],

            // ── Yamaha MT-15 (Rian) ───────────────────────────────────
            [
                'plate'        => 'B 5678 RIB',
                'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
                'days_ago'     => 120,
                'odometer'     => 28000,
                'cost'         => 120000,
                'notes'        => 'Ganti oli Yamalube MAXI Matic, kondisi rantai sedikit kendur.',
                'parts'        => [
                    ['part_name' => 'Yamalube MAXI Matic 10W-40 1L', 'quantity' => 1, 'unit_price' => 90000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Setel Rantai', 'quantity' => 1, 'unit_price' => 30000, 'part_category' => 'Jasa'],
                ],
            ],
            [
                'plate'        => 'B 5678 RIB',
                'service_type' => ServiceRecord::TYPE_BRAKE_SERVICE,
                'days_ago'     => 60,
                'odometer'     => 30000,
                'cost'         => 280000,
                'notes'        => 'Ganti kampas rem depan-belakang, brake fluid diganti.',
                'parts'        => [
                    ['part_name' => 'Kampas Rem Depan Yamaha Original', 'quantity' => 1, 'unit_price' => 85000, 'part_category' => 'Rem'],
                    ['part_name' => 'Kampas Rem Belakang Yamaha Original', 'quantity' => 1, 'unit_price' => 65000, 'part_category' => 'Rem'],
                    ['part_name' => 'Brake Fluid DOT 4 100ml', 'quantity' => 1, 'unit_price' => 30000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Jasa Ganti Kampas Rem', 'quantity' => 2, 'unit_price' => 50000, 'part_category' => 'Jasa'],
                ],
            ],

            // ── Toyota Avanza (Dewi) ───────────────────────────────────
            [
                'plate'        => 'B 2468 DEW',
                'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
                'days_ago'     => 180,
                'odometer'     => 40000,
                'cost'         => 750000,
                'notes'        => 'Servis berkala 40.000 km. Ganti oli mesin, filter oli, filter udara, cek rem.',
                'parts'        => [
                    ['part_name' => 'Oli Toyota Genuine 5W-30 4L', 'quantity' => 1, 'unit_price' => 300000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Filter Oli Toyota Original', 'quantity' => 1, 'unit_price' => 85000, 'part_category' => 'Filter'],
                    ['part_name' => 'Filter Udara Toyota Original', 'quantity' => 1, 'unit_price' => 120000, 'part_category' => 'Filter'],
                    ['part_name' => 'Jasa Servis Berkala', 'quantity' => 1, 'unit_price' => 150000, 'part_category' => 'Jasa'],
                    ['part_name' => 'Cek & Bersihkan Rem', 'quantity' => 1, 'unit_price' => 95000, 'part_category' => 'Jasa'],
                ],
            ],
            [
                'plate'        => 'B 2468 DEW',
                'service_type' => ServiceRecord::TYPE_TIRE_CHANGE,
                'days_ago'     => 90,
                'odometer'     => 50000,
                'cost'         => 1600000,
                'notes'        => 'Ganti 4 ban Bridgestone Ecopia, balancing + spooring.',
                'parts'        => [
                    ['part_name' => 'Ban Bridgestone Ecopia EP150 185/65 R15', 'quantity' => 4, 'unit_price' => 350000, 'part_category' => 'Ban'],
                    ['part_name' => 'Balancing & Spooring', 'quantity' => 1, 'unit_price' => 200000, 'part_category' => 'Jasa'],
                ],
            ],
            [
                'plate'        => 'B 2468 DEW',
                'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
                'days_ago'     => 30,
                'odometer'     => 54000,
                'cost'         => 420000,
                'notes'        => 'Ganti oli mesin dan filter oli. Kondisi AC perlu dicek.',
                'parts'        => [
                    ['part_name' => 'Oli Toyota Genuine 5W-30 4L', 'quantity' => 1, 'unit_price' => 300000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Filter Oli Toyota Original', 'quantity' => 1, 'unit_price' => 85000, 'part_category' => 'Filter'],
                    ['part_name' => 'Jasa Ganti Oli', 'quantity' => 1, 'unit_price' => 35000, 'part_category' => 'Jasa'],
                ],
            ],

            // ── Honda BeAT (Dewi) ─────────────────────────────────────
            [
                'plate'        => 'B 1357 DEX',
                'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
                'days_ago'     => 200,
                'odometer'     => 36000,
                'cost'         => 75000,
                'notes'        => 'Ganti oli AHM MPX1, kondisi CVT berbunyi halus.',
                'parts'        => [
                    ['part_name' => 'Oli AHM MPX1 0.8L', 'quantity' => 1, 'unit_price' => 50000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Jasa Ganti Oli', 'quantity' => 1, 'unit_price' => 25000, 'part_category' => 'Jasa'],
                ],
            ],
            [
                'plate'        => 'B 1357 DEX',
                'service_type' => ServiceRecord::TYPE_REPAIR,
                'days_ago'     => 100,
                'odometer'     => 39000,
                'cost'         => 450000,
                'notes'        => 'Ganti roller CVT dan v-belt. Bunyi kasar pada CVT sudah hilang setelah penggantian.',
                'parts'        => [
                    ['part_name' => 'Roller CVT Honda Original Set', 'quantity' => 1, 'unit_price' => 180000, 'part_category' => 'CVT'],
                    ['part_name' => 'V-Belt Honda Original', 'quantity' => 1, 'unit_price' => 175000, 'part_category' => 'CVT'],
                    ['part_name' => 'Jasa Bongkar Pasang CVT', 'quantity' => 1, 'unit_price' => 95000, 'part_category' => 'Jasa'],
                ],
            ],

            // ── Honda Supra X 125 (Agus) ──────────────────────────────
            [
                'plate'        => 'D 9876 AGS',
                'service_type' => ServiceRecord::TYPE_PERIODIC_SERVICE,
                'days_ago'     => 150,
                'odometer'     => 60000,
                'cost'         => 320000,
                'notes'        => 'Servis besar 60.000 km. Semua kondisi baik, rantai di-lube.',
                'parts'        => [
                    ['part_name' => 'Oli AHM SPX2 10W-30 0.8L', 'quantity' => 1, 'unit_price' => 55000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Filter Udara Honda Original', 'quantity' => 1, 'unit_price' => 45000, 'part_category' => 'Filter'],
                    ['part_name' => 'Busi Honda Original', 'quantity' => 1, 'unit_price' => 22000, 'part_category' => 'Pengapian'],
                    ['part_name' => 'Chain Lube', 'quantity' => 1, 'unit_price' => 48000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Jasa Servis Besar', 'quantity' => 1, 'unit_price' => 150000, 'part_category' => 'Jasa'],
                ],
            ],
            [
                'plate'        => 'D 9876 AGS',
                'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
                'days_ago'     => 30,
                'odometer'     => 66000,
                'cost'         => 80000,
                'notes'        => 'Ganti oli rutin, kondisi keseluruhan sangat baik.',
                'parts'        => [
                    ['part_name' => 'Oli AHM SPX2 10W-30 0.8L', 'quantity' => 1, 'unit_price' => 55000, 'part_category' => 'Pelumas'],
                    ['part_name' => 'Jasa Ganti Oli', 'quantity' => 1, 'unit_price' => 25000, 'part_category' => 'Jasa'],
                ],
            ],
        ];

        $seeded = 0;

        foreach ($records as $entry) {
            $vehicle = $vehicles->get($entry['plate']);
            if (! $vehicle) {
                continue;
            }

            $serviceDate = now()->subDays($entry['days_ago']);

            // Idempotency check
            $exists = ServiceRecord::where('vehicle_id', $vehicle->id)
                ->where('workshop_id', $workshop->id)
                ->where('service_type', $entry['service_type'])
                ->where('odometer_at_service', $entry['odometer'])
                ->exists();

            if ($exists) {
                continue;
            }

            $record = ServiceRecord::create([
                'vehicle_id'          => $vehicle->id,
                'workshop_id'         => $workshop->id,
                'performed_by'        => $workshopUser->id,
                'service_type'        => $entry['service_type'],
                'odometer_at_service' => $entry['odometer'],
                'mechanic_notes'      => $entry['notes'],
                'status'              => ServiceRecord::STATUS_COMPLETED,
                'total_cost'          => $entry['cost'],
                'service_date'        => $serviceDate,
            ]);

            // Seed service parts
            foreach ($entry['parts'] as $part) {
                ServicePart::create([
                    'service_record_id' => $record->id,
                    'part_name'         => $part['part_name'],
                    'quantity'          => $part['quantity'],
                    'unit_price'        => $part['unit_price'],
                    'part_category'     => $part['part_category'],
                ]);
            }

            $seeded++;
        }

        $this->command->info("✅ Service records seeded ({$seeded} records with parts)");
    }
}
