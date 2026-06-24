<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed the default Super Admin account.
     *
     * Credentials:
     *   Email:    admin@maintify.app
     *   Password: password
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@maintify.app'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@maintify.app',
                'password' => Hash::make('password'),
                'role'     => User::ROLE_SUPER_ADMIN,
            ]
        );

        $this->command->info('✅ Super Admin seeded: admin@maintify.app / password');
    }
}
