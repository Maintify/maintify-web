<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Basic Login Screen
    // =========================================================

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_register_link_visible_on_login_page(): void
    {
        $response = $this->get('/login');

        // The login page must contain a link to the register route
        $response->assertSee(route('register'));
    }

    // =========================================================
    // Successful Authentication
    // =========================================================

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_vehicle_owner_redirects_to_dashboard_after_login(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_super_admin_redirects_to_dashboard_after_login(): void
    {
        Mail::fake();
        $user = User::factory()->superAdmin()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $otp = Cache::get("otp:{$user->id}");

        $response = $this->post('/otp-verify', [
            'otp' => $otp,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_workshop_user_with_approved_status_redirects_to_dashboard(): void
    {
        $user = User::factory()->workshop()->create();

        Workshop::create([
            'user_id' => $user->id,
            'name' => 'Test Workshop',
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_workshop_user_with_pending_status_redirects_to_pending_page(): void
    {
        $user = User::factory()->workshop()->create();

        Workshop::create([
            'user_id' => $user->id,
            'name' => 'Test Workshop',
            'status' => Workshop::STATUS_PENDING,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('workshop.pending'));
    }

    public function test_workshop_user_with_rejected_status_redirects_to_pending_page(): void
    {
        $user = User::factory()->workshop()->create();

        Workshop::create([
            'user_id' => $user->id,
            'name' => 'Test Workshop',
            'status' => Workshop::STATUS_REJECTED,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('workshop.pending'));
    }

    public function test_workshop_user_without_workshop_record_redirects_to_pending_page(): void
    {
        // Workshop user with no Workshop row yet → also goes to pending
        $user = User::factory()->workshop()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('workshop.pending'));
    }

    // =========================================================
    // Failed Authentication
    // =========================================================

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_invalid_credentials_show_indonesian_error_message(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertStringContainsString(
            'Email atau password salah',
            $errors->first('email')
        );
    }

    // =========================================================
    // Validation
    // =========================================================

    public function test_login_requires_email(): void
    {
        $response = $this->post('/login', [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // =========================================================
    // Remember Me
    // =========================================================

    public function test_remember_me_sets_remember_token(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $this->assertAuthenticated();

        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    // =========================================================
    // Logout
    // =========================================================

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
