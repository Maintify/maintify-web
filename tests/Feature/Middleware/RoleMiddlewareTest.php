<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Setup — Register temporary test routes
    // =========================================================

    protected function setUp(): void
    {
        parent::setUp();

        // Define test-only routes guarded by the role middleware.
        // These routes do not pollute the real route file.

        Route::middleware(['web', 'auth', 'role:vehicle_owner'])
            ->get('/_test/vehicle-owner-only', fn () => response('vehicle_owner_ok'));

        Route::middleware(['web', 'auth', 'role:workshop'])
            ->get('/_test/workshop-only', fn () => response('workshop_ok'));

        Route::middleware(['web', 'auth', 'role:super_admin'])
            ->get('/_test/super-admin-only', fn () => response('super_admin_ok'));

        Route::middleware(['web', 'auth', 'role:super_admin,workshop'])
            ->get('/_test/admin-or-workshop', fn () => response('admin_or_workshop_ok'));
    }

    // =========================================================
    // Unauthenticated Users → Redirect to Login
    // =========================================================

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/_test/vehicle-owner-only');

        $response->assertRedirect(route('login'));
    }

    // =========================================================
    // Vehicle Owner Access
    // =========================================================

    public function test_vehicle_owner_can_access_vehicle_owner_route(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->actingAs($user)->get('/_test/vehicle-owner-only');

        $response->assertStatus(200);
        $response->assertSee('vehicle_owner_ok');
    }

    public function test_vehicle_owner_cannot_access_workshop_route(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->actingAs($user)->get('/_test/workshop-only');

        $response->assertStatus(403);
    }

    public function test_vehicle_owner_cannot_access_super_admin_route(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->actingAs($user)->get('/_test/super-admin-only');

        $response->assertStatus(403);
    }

    // =========================================================
    // Workshop Access
    // =========================================================

    public function test_workshop_can_access_workshop_route(): void
    {
        $user = User::factory()->workshop()->create();

        $response = $this->actingAs($user)->get('/_test/workshop-only');

        $response->assertStatus(200);
        $response->assertSee('workshop_ok');
    }

    public function test_workshop_cannot_access_vehicle_owner_route(): void
    {
        $user = User::factory()->workshop()->create();

        $response = $this->actingAs($user)->get('/_test/vehicle-owner-only');

        $response->assertStatus(403);
    }

    public function test_workshop_cannot_access_super_admin_route(): void
    {
        $user = User::factory()->workshop()->create();

        $response = $this->actingAs($user)->get('/_test/super-admin-only');

        $response->assertStatus(403);
    }

    // =========================================================
    // Super Admin Access
    // =========================================================

    public function test_super_admin_can_access_super_admin_route(): void
    {
        $user = User::factory()->superAdmin()->create();

        $response = $this->actingAs($user)->get('/_test/super-admin-only');

        $response->assertStatus(200);
        $response->assertSee('super_admin_ok');
    }

    public function test_super_admin_cannot_access_vehicle_owner_route(): void
    {
        $user = User::factory()->superAdmin()->create();

        $response = $this->actingAs($user)->get('/_test/vehicle-owner-only');

        $response->assertStatus(403);
    }

    public function test_super_admin_cannot_access_workshop_route(): void
    {
        $user = User::factory()->superAdmin()->create();

        $response = $this->actingAs($user)->get('/_test/workshop-only');

        $response->assertStatus(403);
    }

    // =========================================================
    // Multi-Role Access (e.g. role:super_admin,workshop)
    // =========================================================

    public function test_super_admin_can_access_multi_role_route(): void
    {
        $user = User::factory()->superAdmin()->create();

        $response = $this->actingAs($user)->get('/_test/admin-or-workshop');

        $response->assertStatus(200);
        $response->assertSee('admin_or_workshop_ok');
    }

    public function test_workshop_can_access_multi_role_route(): void
    {
        $user = User::factory()->workshop()->create();

        $response = $this->actingAs($user)->get('/_test/admin-or-workshop');

        $response->assertStatus(200);
        $response->assertSee('admin_or_workshop_ok');
    }

    public function test_vehicle_owner_cannot_access_multi_role_route(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->actingAs($user)->get('/_test/admin-or-workshop');

        $response->assertStatus(403);
    }

    // =========================================================
    // 403 Error Message
    // =========================================================

    public function test_forbidden_response_contains_indonesian_error_message(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->actingAs($user)->get('/_test/super-admin-only');

        $response->assertStatus(403);
    }
}
