<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Rendering
    // =========================================================

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    // =========================================================
    // Successful Registration
    // =========================================================

    public function test_new_users_can_register_as_vehicle_owner(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'vehicle_owner',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'vehicle_owner',
        ]);
    }

    public function test_new_users_can_register_without_role_defaults_to_vehicle_owner(): void
    {
        $response = $this->post('/register', [
            'name' => 'Default Role User',
            'email' => 'default@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'default@example.com',
            'role' => 'vehicle_owner',
        ]);
    }

    /**
     * Alias kept for backward compatibility with original test name.
     */
    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_new_workshop_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Workshop',
            'email' => 'workshop@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'workshop',
        ]);

        $this->assertAuthenticated();

        // Workshop users are redirected to register.workshop if route exists,
        // otherwise fall back to dashboard.
        if (\Illuminate\Support\Facades\Route::has('register.workshop')) {
            $response->assertRedirect(route('register.workshop'));
        } else {
            $response->assertRedirect(route('dashboard'));
        }

        $this->assertDatabaseHas('users', [
            'email' => 'workshop@example.com',
            'role' => 'workshop',
        ]);
    }

    // =========================================================
    // Validation: Required Fields
    // =========================================================

    public function test_registration_requires_name(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    public function test_registration_requires_valid_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'not-a-valid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_prevents_duplicate_email(): void
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->post('/register', [
            'name' => 'Another User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different_password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_registration_requires_minimum_password_length(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'abc',
            'password_confirmation' => 'abc',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    // =========================================================
    // Security
    // =========================================================

    public function test_password_is_stored_as_bcrypt_hash(): void
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password', $user->password));
    }

    // =========================================================
    // Role Validation
    // =========================================================

    public function test_invalid_role_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'super_admin',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
    }
}
