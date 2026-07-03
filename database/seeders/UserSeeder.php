<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed development users.
     *
     * Creates:
     *   - 1 Super Admin  (sudah ditangani AdminSeeder, di sini hanya jika belum ada)
     *   - 2 Workshop admins
     *   - 3 Vehicle owners
     *
     * Seeder ini idempotent — menggunakan firstOrCreate.
     */
    public function run(): void
    {
        // ────────────────────────────────────────────────
        // Super Admin (fallback jika AdminSeeder belum jalan)
        // ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@maintify.app'],
            [
                'name'     => 'Super Admin',
                'role'     => User::ROLE_SUPER_ADMIN,
                'password' => Hash::make('password'),
            ]
        );

        // ────────────────────────────────────────────────
        // Workshop Admin 1 — Bengkel Maju Jaya (approved)
        // ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'bengkel.majujaya@maintify.app'],
            [
                'name'     => 'Hendra Wijaya',
                'role'     => User::ROLE_WORKSHOP,
                'password' => Hash::make('password'),
            ]
        );

        // ────────────────────────────────────────────────
        // Workshop Admin 2 — Bengkel Sentosa (pending)
        // ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'bengkel.sentosa@maintify.app'],
            [
                'name'     => 'Budi Santoso',
                'role'     => User::ROLE_WORKSHOP,
                'password' => Hash::make('password'),
            ]
        );

        // ────────────────────────────────────────────────
        // Vehicle Owner 1
        // ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'rian@maintify.app'],
            [
                'name'     => 'Rian Pratama',
                'role'     => User::ROLE_VEHICLE_OWNER,
                'password' => Hash::make('password'),
            ]
        );

        // ────────────────────────────────────────────────
        // Vehicle Owner 2
        // ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'dewi@maintify.app'],
            [
                'name'     => 'Dewi Anggraini',
                'role'     => User::ROLE_VEHICLE_OWNER,
                'password' => Hash::make('password'),
            ]
        );

        // ────────────────────────────────────────────────
        // Vehicle Owner 3
        // ────────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'agus@maintify.app'],
            [
                'name'     => 'Agus Setiawan',
                'role'     => User::ROLE_VEHICLE_OWNER,
                'password' => Hash::make('password'),
            ]
        );

        $this->command->info('✅ Users seeded (1 super_admin, 2 workshop, 3 vehicle_owner)');
    }
}
