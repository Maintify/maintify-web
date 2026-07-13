<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function super_admin_can_view_users_list()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user1 = User::factory()->create(['name' => 'Alice Margatroid', 'role' => User::ROLE_VEHICLE_OWNER]);
        $user2 = User::factory()->create(['name' => 'Marisa Kirisame', 'role' => User::ROLE_WORKSHOP]);

        $response = $this->actingAs($superAdmin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Alice Margatroid');
        $response->assertSee('Marisa Kirisame');
    }

    /** @test */
    public function super_admin_can_filter_users_by_role()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['name' => 'Alice Owner', 'role' => User::ROLE_VEHICLE_OWNER]);
        $workshop = User::factory()->create(['name' => 'Bob Workshop', 'role' => User::ROLE_WORKSHOP]);

        $response = $this->actingAs($superAdmin)->get(route('admin.users.index', ['role' => User::ROLE_VEHICLE_OWNER]));

        $response->assertStatus(200);
        $response->assertSee('Alice Owner');
        $response->assertDontSee('Bob Workshop');
    }

    /** @test */
    public function super_admin_can_search_users_by_name_or_email()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user1 = User::factory()->create(['name' => 'Alice Cooper', 'email' => 'alice@cooper.com']);
        $user2 = User::factory()->create(['name' => 'Charlie Chaplin', 'email' => 'charlie@chaplin.com']);

        $response = $this->actingAs($superAdmin)->get(route('admin.users.index', ['search' => 'Alice']));

        $response->assertStatus(200);
        $response->assertSee('Alice Cooper');
        $response->assertDontSee('Charlie Chaplin');
    }

    /** @test */
    public function super_admin_can_view_user_details()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user = User::factory()->create(['name' => 'Alice Cooper', 'email' => 'alice@cooper.com']);

        $response = $this->actingAs($superAdmin)->get(route('admin.users.show', $user->id));

        $response->assertStatus(200);
        $response->assertSee('Alice Cooper');
        $response->assertSee('alice@cooper.com');
    }

    /** @test */
    public function super_admin_can_deactivate_user()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user = User::factory()->create([
            'name' => 'Alice Cooper',
            'email' => 'alice@cooper.com',
            'password' => 'password123',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $user->id), [
            'is_active' => 0,
        ]);

        $response->assertRedirect();

        // Assert updated in DB
        $user->refresh();
        $this->assertFalse($user->is_active);

        // Assert audit log created
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'user_deactivate',
            'entity_type' => 'users',
            'entity_id' => $user->id,
        ]);

        // Log out Super Admin first so login endpoint is reached
        auth()->logout();

        // Assert deactivated user cannot login
        $loginResponse = $this->post('/login', [
            'email' => 'alice@cooper.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertSessionHasErrors(['email']);
        $this->assertFalse(auth()->check());
    }

    /** @test */
    public function super_admin_can_activate_user()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $user = User::factory()->create([
            'name' => 'Alice Cooper',
            'email' => 'alice@cooper.com',
            'is_active' => false,
        ]);

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $user->id), [
            'is_active' => 1,
        ]);

        $response->assertRedirect();

        // Assert updated in DB
        $user->refresh();
        $this->assertTrue($user->is_active);

        // Assert audit log created
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'user_activate',
            'entity_type' => 'users',
            'entity_id' => $user->id,
        ]);
    }

    /** @test */
    public function super_admin_cannot_deactivate_self()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN, 'is_active' => true]);

        $response = $this->actingAs($superAdmin)->put(route('admin.users.update', $superAdmin->id), [
            'is_active' => 0,
        ]);

        $response->assertSessionHasErrors(['is_active']);

        // Assert remains active in DB
        $superAdmin->refresh();
        $this->assertTrue($superAdmin->is_active);
    }

    /** @test */
    public function non_super_admin_cannot_access_user_management()
    {
        $regularUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $targetUser = User::factory()->create();

        // Index
        $this->actingAs($regularUser)->get(route('admin.users.index'))->assertStatus(403);

        // Show
        $this->actingAs($regularUser)->get(route('admin.users.show', $targetUser->id))->assertStatus(403);

        // Update
        $this->actingAs($regularUser)->put(route('admin.users.update', $targetUser->id), [
            'is_active' => 0,
        ])->assertStatus(403);
    }
}
