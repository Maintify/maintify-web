<?php

namespace Tests\Feature\Security;

use App\Models\Notification;
use App\Models\OwnershipTransfer;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use App\Providers\AppServiceProvider;
use App\Services\FileUploadService;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/**
 * Basic Penetration Testing Suite (Task 14.2.1).
 *
 * Verifies the four security controls required by the task:
 *   1. RBAC        — role-based access control on sensitive endpoints.
 *   2. IDOR        — object-level ownership enforcement.
 *   3. CSRF        — token validation on state-changing requests.
 *   4. SQLi        — SQL injection prevention on search/filter inputs.
 *   5. XSS         — stored/reflected script injection is neutralised.
 *   6. Priv-esc    — role cannot be elevated via mass assignment.
 *
 * These tests are written from an attacker's perspective: each one attempts
 * an unauthorized action and asserts the application blocks it.
 */
class PenetrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /**
     * Helper: create an approved workshop owned by a fresh workshop user.
     */
    private function approvedWorkshop(?User $owner = null): Workshop
    {
        $owner ??= User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        return Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Bengkel '.fake()->lastName(),
            'phone' => '081234567890',
            'email' => fake()->unique()->safeEmail(),
            'address' => 'Jl. Test No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);
    }

    // =========================================================
    // 1. RBAC — Role-Based Access Control
    // =========================================================

    /** @test */
    public function vehicle_owner_cannot_access_super_admin_endpoints(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $this->actingAs($user)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.workshops.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.audit-logs.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('admin.settings.index'))->assertStatus(403);
    }

    /** @test */
    public function workshop_user_cannot_access_super_admin_endpoints(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $this->approvedWorkshop($owner);

        $this->actingAs($owner)->get(route('admin.users.index'))->assertStatus(403);
        $this->actingAs($owner)->get(route('admin.settings.index'))->assertStatus(403);
    }

    /** @test */
    public function vehicle_owner_cannot_access_workshop_endpoints(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $this->actingAs($user)->get(route('workshop.scan'))->assertStatus(403);
        $this->actingAs($user)->get(route('workshop.reports.index'))->assertStatus(403);
        $this->actingAs($user)->get(route('workshop.customers.index'))->assertStatus(403);
    }

    /** @test */
    public function workshop_user_cannot_access_vehicle_owner_endpoints(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $this->approvedWorkshop($owner);

        $this->actingAs($owner)->get(route('vehicles.index'))->assertStatus(403);
        $this->actingAs($owner)->get(route('vehicles.create'))->assertStatus(403);
    }

    /** @test */
    public function super_admin_cannot_deactivate_their_own_account(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($admin)
            ->from(route('admin.users.show', $admin))
            ->put(route('admin.users.update', $admin), ['is_active' => false]);

        $response->assertSessionHasErrors('is_active');
        $this->assertTrue($admin->fresh()->is_active);
    }

    /** @test */
    public function guest_is_redirected_from_protected_endpoints(): void
    {
        $this->get(route('vehicles.index'))->assertRedirect(route('login'));
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    // =========================================================
    // 2. IDOR — Object-Level Ownership Enforcement
    // =========================================================

    /** @test */
    public function user_cannot_view_or_edit_another_users_vehicle(): void
    {
        $attacker = User::factory()->vehicleOwner()->create();
        $victim = User::factory()->vehicleOwner()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $victim->id]);

        $this->actingAs($attacker)->get(route('vehicles.show', $vehicle))->assertStatus(403);
        $this->actingAs($attacker)->get(route('vehicles.edit', $vehicle))->assertStatus(403);
        $this->actingAs($attacker)->get(route('vehicles.qr.show', $vehicle))->assertStatus(403);
        $this->actingAs($attacker)->post(route('vehicles.qr.regenerate', $vehicle))->assertStatus(403);
    }

    /** @test */
    public function user_cannot_update_another_users_vehicle(): void
    {
        $attacker = User::factory()->vehicleOwner()->create();
        $victim = User::factory()->vehicleOwner()->create();
        $vehicle = Vehicle::factory()->create([
            'user_id' => $victim->id,
            'brand' => 'Honda',
        ]);

        $this->actingAs($attacker)->put(route('vehicles.update', $vehicle), [
            'brand' => 'Hacked',
            'model' => $vehicle->model,
            'plate_number' => $vehicle->plate_number,
            'year' => $vehicle->year,
        ])->assertStatus(403);

        $this->assertSame('Honda', $vehicle->fresh()->brand);
    }

    /** @test */
    public function user_cannot_mark_another_users_notification_as_read(): void
    {
        $attacker = User::factory()->vehicleOwner()->create();
        $victim = User::factory()->vehicleOwner()->create();
        $notification = Notification::create([
            'user_id' => $victim->id,
            'type' => 'test',
            'title' => 'Rahasia',
            'message' => 'Pesan rahasia korban',
            'is_read' => false,
        ]);

        $this->actingAs($attacker)
            ->post(route('notifications.read', $notification))
            ->assertStatus(403);

        $this->assertFalse((bool) $notification->fresh()->is_read);
    }

    /** @test */
    public function recipient_check_blocks_unauthorized_transfer_approval(): void
    {
        $owner = User::factory()->vehicleOwner()->create();
        $recipient = User::factory()->vehicleOwner()->create();
        $attacker = User::factory()->vehicleOwner()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $owner->id]);

        $transfer = OwnershipTransfer::create([
            'vehicle_id' => $vehicle->id,
            'from_user_id' => $owner->id,
            'to_user_id' => $recipient->id,
            'status' => OwnershipTransfer::STATUS_PENDING_RECIPIENT,
            'requested_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);

        // Attacker (neither owner nor recipient) tries to approve.
        $this->actingAs($attacker)
            ->from(route('vehicles.show', $vehicle))
            ->post(route('transfers.approve', $transfer))
            ->assertRedirect();

        // Ownership must not have transferred and status must be unchanged.
        $this->assertSame(OwnershipTransfer::STATUS_PENDING_RECIPIENT, $transfer->fresh()->status);
        $this->assertSame($owner->id, $vehicle->fresh()->user_id);
    }

    /** @test */
    public function workshop_cannot_edit_service_record_of_another_workshop(): void
    {
        $workshopA = $this->approvedWorkshop();
        $workshopB = $this->approvedWorkshop();
        $ownerB = $workshopB->user;

        $vehicle = Vehicle::factory()->create();
        $record = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshopA->id,
            'performed_by' => $workshopA->user_id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 1000,
            'status' => 'completed',
            'total_cost' => 100000,
            'service_date' => now()->subDay(),
        ]);

        $this->actingAs($ownerB)
            ->get(route('workshop.service-records.edit', $record))
            ->assertStatus(403);
    }

    // =========================================================
    // 3. CSRF — Token Validation on State-Changing Requests
    // =========================================================

    /**
     * The test environment disables CSRF verification, so we cannot assert a
     * 419 by firing a request. Instead we assert the guarantee that matters:
     * the CSRF middleware is actually attached to the `web` group that every
     * state-changing web route runs through. If someone removes it, this fails.
     *
     * @test
     */
    public function csrf_middleware_is_registered_on_the_web_group(): void
    {
        $kernel = $this->app->make(Kernel::class);
        $webMiddleware = $kernel->getMiddlewareGroups()['web'] ?? [];

        $this->assertContains(
            ValidateCsrfToken::class,
            $webMiddleware,
            'CSRF protection must be enabled on all web routes.'
        );
    }

    /**
     * Verify no route bypasses CSRF via an exception list. An empty `$except`
     * array on the CSRF middleware means every POST/PUT/PATCH/DELETE is guarded.
     *
     * @test
     */
    public function no_web_routes_are_excluded_from_csrf_protection(): void
    {
        $reflection = new \ReflectionClass(ValidateCsrfToken::class);
        $property = $reflection->getProperty('except');
        $property->setAccessible(true);

        /** @var ValidateCsrfToken $instance */
        $instance = $this->app->make(ValidateCsrfToken::class);

        $this->assertEmpty(
            $property->getValue($instance),
            'No routes should be excluded from CSRF verification.'
        );
    }

    // =========================================================
    // 4. SQL Injection Prevention
    // =========================================================

    /** @test */
    public function sql_injection_in_vehicle_search_is_neutralised(): void
    {
        $user = User::factory()->vehicleOwner()->create();
        Vehicle::factory()->create(['user_id' => $user->id, 'brand' => 'Honda']);

        $payload = "'; DROP TABLE vehicles; --";

        $response = $this->actingAs($user)->get(route('vehicles.index', ['search' => $payload]));

        $response->assertStatus(200);
        // Table must still exist and be queryable.
        $this->assertDatabaseHas('vehicles', ['user_id' => $user->id]);
    }

    /** @test */
    public function sql_injection_in_admin_user_search_is_neutralised(): void
    {
        $admin = User::factory()->superAdmin()->create();

        $payload = "%' OR '1'='1";

        $response = $this->actingAs($admin)->get(route('admin.users.index', ['search' => $payload]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /** @test */
    public function sql_injection_in_workshop_search_api_is_neutralised(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->actingAs($user)->getJson(route('api.workshops.nearby', [
            'service_type' => "oil'; DROP TABLE workshops; --",
        ]));

        // Either a valid empty result or a validation response — never a 500 from broken SQL.
        $this->assertContains($response->getStatusCode(), [200, 422]);
        $this->assertDatabaseCount('users', 1);
    }

    // =========================================================
    // 5. XSS Prevention
    // =========================================================

    /** @test */
    public function stored_xss_in_vehicle_fields_is_escaped_in_output(): void
    {
        $user = User::factory()->vehicleOwner()->create();
        $xss = '<script>alert("xss")</script>';
        $vehicle = Vehicle::factory()->create([
            'user_id' => $user->id,
            'brand' => $xss,
            'model' => 'Test',
        ]);

        $response = $this->actingAs($user)->get(route('vehicles.show', $vehicle));

        $response->assertStatus(200);
        // Raw script tag must NOT appear; Blade should HTML-escape it.
        $response->assertDontSee($xss, false);
        $response->assertSee('&lt;script&gt;', false);
    }

    /** @test */
    public function reflected_xss_in_search_input_is_escaped(): void
    {
        $user = User::factory()->vehicleOwner()->create();
        $xss = '<script>alert(1)</script>';

        $response = $this->actingAs($user)->get(route('vehicles.index', ['search' => $xss]));

        $response->assertStatus(200);
        $response->assertDontSee($xss, false);
    }

    // =========================================================
    // 6. Privilege Escalation via Mass Assignment
    // =========================================================

    /** @test */
    public function user_cannot_self_register_as_super_admin(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Attacker',
            'email' => 'attacker@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'super_admin',
        ]);

        // Validation rejects the disallowed role value.
        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', [
            'email' => 'attacker@test.com',
            'role' => 'super_admin',
        ]);
    }

    /** @test */
    public function profile_update_cannot_escalate_role(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'super_admin',
        ]);

        $this->assertSame(User::ROLE_VEHICLE_OWNER, $user->fresh()->role);
    }

    // =========================================================
    // 7. Production Hardening — Secure Config Enforcement
    // =========================================================

    /**
     * Boot AppServiceProvider as if the app were in production, starting from
     * deliberately insecure config, and return the app so callers can assert the
     * hardening took effect. This tests the hardening logic in isolation without
     * rebooting the kernel (which would re-read .env and reset the environment).
     */
    private function bootProviderInProduction(): void
    {
        $this->app['env'] = 'production';
        $this->app['config']->set('app.debug', true);
        $this->app['config']->set('session.secure', null);
        $this->app['config']->set('session.http_only', false);

        (new AppServiceProvider($this->app))->boot();
    }

    /**
     * When the app runs in production, AppServiceProvider must force secure and
     * http-only session cookies regardless of the deployment .env, so cookies
     * are never sent over plain HTTP or exposed to JavaScript.
     *
     * @test
     */
    public function production_environment_forces_secure_session_cookies(): void
    {
        $this->bootProviderInProduction();

        $this->assertTrue($this->app['config']->get('session.secure'), 'session.secure must be true in production.');
        $this->assertTrue($this->app['config']->get('session.http_only'), 'session.http_only must be true in production.');
        $this->assertContains($this->app['config']->get('session.same_site'), ['lax', 'strict'], 'same_site must restrict cross-site sending.');
    }

    /**
     * APP_DEBUG must be forced off in production even if the .env leaves it on,
     * to prevent stack-trace / config disclosure via error pages.
     *
     * @test
     */
    public function production_environment_forces_debug_off(): void
    {
        $this->bootProviderInProduction();

        $this->assertFalse($this->app['config']->get('app.debug'), 'app.debug must be false in production.');
    }

    // =========================================================
    // 8. File Upload Security
    // =========================================================

    /**
     * A PHP script renamed with a .jpg extension must be rejected. The service
     * validates the real MIME type, not just the extension, so a disguised
     * executable never reaches storage.
     *
     * @test
     */
    public function disguised_php_script_upload_is_rejected(): void
    {
        $service = new FileUploadService;

        // A .jpg extension but a real MIME of text/x-php. The service checks the
        // real MIME type via getMimeType(), so the disguised script is rejected.
        $malicious = UploadedFile::fake()->create('shell.jpg', 1, 'text/x-php');

        $this->expectException(\InvalidArgumentException::class);
        $service->uploadVehiclePhoto($malicious);
    }

    /**
     * A file whose real MIME type is not an allowed image must be rejected,
     * even when the extension looks like an image. Guards mimes-spoofing.
     *
     * @test
     */
    public function upload_with_non_image_mime_is_rejected(): void
    {
        $service = new FileUploadService;

        $pdf = UploadedFile::fake()->create('document.png', 1, 'application/pdf');

        $this->expectException(\InvalidArgumentException::class);
        $service->uploadVehiclePhoto($pdf);
    }

    /**
     * The vehicle store request must reject a non-image upload at the HTTP
     * validation layer (defense in depth, before the service is even called).
     *
     * @test
     */
    public function vehicle_form_rejects_non_image_photo_upload(): void
    {
        $user = User::factory()->vehicleOwner()->create();

        $response = $this->actingAs($user)->post(route('vehicles.store'), [
            'plate_number' => 'B 9999 ZZ',
            'brand' => 'Honda',
            'model' => 'Test',
            'year' => 2020,
            'fuel_type' => 'gasoline',
            'chassis_number' => 'ABCDEFGH12345678X',
            'current_odometer' => 100,
            'photo' => UploadedFile::fake()->createWithContent(
                'evil.php',
                "<?php echo 'pwned'; ?>"
            ),
        ]);

        $response->assertSessionHasErrors('photo');
        $this->assertDatabaseMissing('vehicles', ['plate_number' => 'B 9999 ZZ']);
    }
}
