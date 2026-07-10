<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_session_expires_after_30_minutes_of_inactivity(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->actingAs($admin);

        // Simulate activity 31 minutes ago
        session(['last_activity_time' => time() - 1860]);

        $response = $this->get('/dashboard');

        // Should be logged out and redirected to login with error message
        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Sesi Anda telah berakhir karena tidak ada aktivitas.');
    }

    public function test_workshop_session_expires_after_30_minutes_of_inactivity(): void
    {
        $workshop = User::factory()->create([
            'role' => User::ROLE_WORKSHOP,
        ]);

        $this->actingAs($workshop);

        // Simulate activity 31 minutes ago
        session(['last_activity_time' => time() - 1860]);

        $response = $this->get('/dashboard');

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Sesi Anda telah berakhir karena tidak ada aktivitas.');
    }

    public function test_vehicle_owner_session_does_not_expire_after_30_minutes(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
        ]);

        $this->actingAs($user);

        // Simulate activity 31 minutes ago
        session(['last_activity_time' => time() - 1860]);

        $response = $this->get('/dashboard');

        // Should still be authenticated
        $this->assertAuthenticatedAs($user);
        $response->assertStatus(200);
    }
    
    public function test_admin_session_is_maintained_with_activity(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $this->actingAs($admin);

        // Simulate activity 10 minutes ago
        session(['last_activity_time' => time() - 600]);

        $response = $this->get('/dashboard');

        // Should still be authenticated
        $this->assertAuthenticatedAs($admin);
        $response->assertStatus(200);
    }
}
