<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order matters — each seeder depends on the previous ones:
     *   1. UserSeeder         — creates all user accounts
     *   2. WorkshopSeeder     — creates workshops + verification records (needs users)
     *   3. VehicleSeeder      — creates vehicles + QR codes (needs vehicle_owner users)
     *   4. ServiceRecordSeeder — creates service records + parts (needs vehicles & workshops)
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            WorkshopSeeder::class,
            VehicleSeeder::class,
            ServiceRecordSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('🚀 All seeders completed successfully.');
        $this->command->newLine();
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin',    'admin@maintify.app',              'password'],
                ['Workshop (approved)', 'bengkel.majujaya@maintify.app', 'password'],
                ['Workshop (pending)',  'bengkel.sentosa@maintify.app',  'password'],
                ['Vehicle Owner',  'rian@maintify.app',               'password'],
                ['Vehicle Owner',  'dewi@maintify.app',               'password'],
                ['Vehicle Owner',  'agus@maintify.app',               'password'],
            ]
        );
    }
}
