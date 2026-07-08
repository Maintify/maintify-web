<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopVerification;
use Illuminate\Database\Seeder;

class WorkshopSeeder extends Seeder
{
    /**
     * Seed sample workshops.
     *
     * Creates:
     *   - 1 workshop dengan status 'approved'
     *   - 1 workshop dengan status 'pending'
     *
     * Idempotent — skip jika user sudah memiliki workshop.
     */
    public function run(): void
    {
        $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->first();
        $workshopUser1 = User::where('email', 'bengkel.majujaya@maintify.app')->first();
        $workshopUser2 = User::where('email', 'bengkel.sentosa@maintify.app')->first();

        if (! $workshopUser1 || ! $workshopUser2) {
            $this->command->warn('⚠️  Workshop users not found. Run UserSeeder first.');

            return;
        }

        // ────────────────────────────────────────────────
        // Workshop 1 — Bengkel Maju Jaya (APPROVED)
        // ────────────────────────────────────────────────
        $workshop1 = Workshop::firstOrCreate(
            ['user_id' => $workshopUser1->id],
            [
                'name' => 'Bengkel Maju Jaya',
                'phone' => '08123456789',
                'email' => 'info@bengkelmajujaya.com',
                'address' => 'Jl. Raya Kebon Jeruk No. 45',
                'city' => 'Jakarta Barat',
                'province' => 'DKI Jakarta',
                'postal_code' => '11530',
                'description' => 'Bengkel resmi kendaraan bermotor roda dua dan empat dengan pengalaman lebih dari 10 tahun.',
                'owner_name' => 'Budi Setiawan',
                'owner_ktp_number' => '3173012345670001',
                'legal_document_url' => 'documents/bengkel_maju_jaya_nib.pdf',
                'latitude' => -6.193125,
                'longitude' => 106.772541,
                'rating_average' => 4.8,
                'operational_hours' => 'Senin - Sabtu: 08:00 - 17:00',
                'is_active' => true,
                'status' => Workshop::STATUS_APPROVED,
                'approved_at' => now()->subDays(30),
                'approved_by' => $superAdmin?->id,
            ]
        );

        // Workshop verification record untuk workshop 1
        WorkshopVerification::firstOrCreate(
            ['workshop_id' => $workshop1->id],
            [
                'reviewed_by' => $superAdmin?->id,
                'status' => 'approved',
                'rejection_reason' => null,
                'reviewed_at' => now()->subDays(30),
            ]
        );

        // ────────────────────────────────────────────────
        // Workshop 2 — Bengkel Sentosa Motor (PENDING)
        // ────────────────────────────────────────────────
        $workshop2 = Workshop::firstOrCreate(
            ['user_id' => $workshopUser2->id],
            [
                'name' => 'Bengkel Sentosa Motor',
                'phone' => '08567891234',
                'email' => 'sentosamotor@gmail.com',
                'address' => 'Jl. Pemuda No. 12, RT 03/RW 05',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60271',
                'description' => 'Spesialisasi service motor matic dan bebek.',
                'owner_name' => 'Hendro Santoso',
                'owner_ktp_number' => '3578012345670002',
                'legal_document_url' => 'documents/bengkel_sentosa_nib.pdf',
                'latitude' => -7.265241,
                'longitude' => 112.751682,
                'rating_average' => 4.5,
                'operational_hours' => 'Setiap Hari: 08:00 - 18:00',
                'is_active' => false,
                'status' => Workshop::STATUS_PENDING,
                'approved_at' => null,
                'approved_by' => null,
            ]
        );

        // Workshop verification record untuk workshop 2 (pending)
        WorkshopVerification::firstOrCreate(
            ['workshop_id' => $workshop2->id],
            [
                'reviewed_by' => null,
                'status' => 'pending',
                'rejection_reason' => null,
                'reviewed_at' => null,
            ]
        );

        // Seed spareparts for Workshop 1
        $spareparts = [
            ['name' => 'Oli Shell Helix HX7 10W-40', 'category' => 'Oli', 'price' => 95000.00],
            ['name' => 'Oli MPX 2 10W-30', 'category' => 'Oli', 'price' => 52000.00],
            ['name' => 'Kampas Rem Depan Honda Vario', 'category' => 'Rem', 'price' => 65000.00],
            ['name' => 'Kampas Rem Belakang Honda Vario', 'category' => 'Rem', 'price' => 55000.00],
            ['name' => 'Busi NGK Iridium', 'category' => 'Kelistrikan', 'price' => 120000.00],
            ['name' => 'Filter Udara Vario 160', 'category' => 'Filter', 'price' => 75000.00],
            ['name' => 'V-Belt Kit Honda Vario 150', 'category' => 'Transmisi', 'price' => 185000.00],
        ];

        foreach ($spareparts as $part) {
            \App\Models\Sparepart::firstOrCreate(
                [
                    'workshop_id' => $workshop1->id,
                    'name' => $part['name'],
                ],
                [
                    'category' => $part['category'],
                    'price' => $part['price'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Workshops seeded (1 approved: Bengkel Maju Jaya, 1 pending: Bengkel Sentosa Motor)');
    }
}
