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

        $this->command->info('✅ Workshops seeded (1 approved: Bengkel Maju Jaya, 1 pending: Bengkel Sentosa Motor)');
    }
}
