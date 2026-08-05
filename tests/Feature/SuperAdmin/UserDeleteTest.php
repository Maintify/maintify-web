<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $targetUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->targetUser = User::factory()->create([
            'role' => 'vehicle_owner',
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_delete_user_account(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.destroy', $this->targetUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $this->targetUser->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $this->superAdmin->id,
            'action' => 'user_delete',
            'entity_id' => $this->targetUser->id,
        ]);
    }

    public function test_super_admin_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.users.destroy', $this->superAdmin));

        $response->assertRedirect();
        $response->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', [
            'id' => $this->superAdmin->id,
        ]);
    }

    public function test_non_admin_users_cannot_delete_user_accounts(): void
    {
        $vehicleOwner = User::factory()->create(['role' => 'vehicle_owner']);

        $response = $this->actingAs($vehicleOwner)
            ->delete(route('admin.users.destroy', $this->targetUser));

        $response->assertStatus(403);

        $this->assertDatabaseHas('users', [
            'id' => $this->targetUser->id,
        ]);
    }
}
