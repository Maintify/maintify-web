<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function super_admin_can_view_audit_logs_list()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        
        $user = User::factory()->create(['name' => 'John Doe']);
        
        // Seed logs using AuditLog::create directly to avoid using auth() if needed,
        // or record with actingAs
        $this->actingAs($superAdmin);

        AuditLog::create([
            'actor_user_id' => $user->id,
            'action' => 'user_deactivate',
            'entity_type' => 'users',
            'entity_id' => 12,
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        AuditLog::create([
            'actor_user_id' => $superAdmin->id,
            'action' => 'workshop_approve',
            'entity_type' => 'workshops',
            'entity_id' => 3,
            'ip_address' => '192.168.1.1',
            'created_at' => now(),
        ]);

        $response = $this->get(route('admin.audit-logs.index'));

        $response->assertStatus(200);
        $response->assertSee('user_deactivate');
        $response->assertSee('workshop_approve');
        $response->assertSee('John Doe');
        $response->assertSee('192.168.1.1');
    }

    /** @test */
    public function super_admin_can_filter_audit_logs()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        
        $andi = User::factory()->create(['name' => 'Andi Susanto', 'email' => 'andi@susanto.com']);
        $budi = User::factory()->create(['name' => 'Budi Handoko', 'email' => 'budi@handoko.com']);

        $log1 = new AuditLog([
            'actor_user_id' => $andi->id,
            'action' => 'user_deactivate',
            'entity_type' => 'users',
            'entity_id' => 10,
        ]);
        $log1->created_at = '2026-07-08 12:00:00';
        $log1->save();

        $log2 = new AuditLog([
            'actor_user_id' => $budi->id,
            'action' => 'workshop_approve',
            'entity_type' => 'workshops',
            'entity_id' => 5,
        ]);
        $log2->created_at = '2026-07-09 14:00:00';
        $log2->save();

        $this->actingAs($superAdmin);

        // Filter: Actor search
        $response = $this->get(route('admin.audit-logs.index', ['actor_search' => 'Andi']));
        $response->assertStatus(200);
        $response->assertSee('andi@susanto.com');
        $response->assertDontSee('budi@handoko.com');

        // Filter: Action
        $response = $this->get(route('admin.audit-logs.index', ['action' => 'workshop_approve']));
        $response->assertStatus(200);
        $response->assertSee('budi@handoko.com');
        $response->assertDontSee('andi@susanto.com');

        // Filter: Entity
        $response = $this->get(route('admin.audit-logs.index', ['entity_type' => 'users']));
        $response->assertStatus(200);
        $response->assertSee('andi@susanto.com');
        $response->assertDontSee('budi@handoko.com');

        // Filter: Date Range (start_date)
        $response = $this->get(route('admin.audit-logs.index', ['start_date' => '2026-07-09']));
        $response->assertStatus(200);
        $response->assertSee('budi@handoko.com');
        $response->assertDontSee('andi@susanto.com');
    }

    /** @test */
    public function super_admin_can_view_detailed_log_metadata()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        
        $log = AuditLog::create([
            'actor_user_id' => $superAdmin->id,
            'action' => 'user_deactivate',
            'entity_type' => 'users',
            'entity_id' => 15,
            'metadata' => [
                'reason' => 'Pelanggaran ketentuan sistem.',
                'notified' => true,
            ],
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.audit-logs.show', $log->id));

        $response->assertStatus(200);
        $response->assertSee('Pelanggaran ketentuan sistem.');
        $response->assertSee('notified');
        $response->assertSee('127.0.0.1');
    }

    /** @test */
    public function super_admin_has_read_only_access_to_logs()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        
        $log = AuditLog::create([
            'actor_user_id' => $superAdmin->id,
            'action' => 'user_deactivate',
            'entity_type' => 'users',
            'entity_id' => 15,
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $this->actingAs($superAdmin);

        // POST (Store) -> should be 404 or 405
        $responseStore = $this->post('/admin/audit-logs', [
            'action' => 'malicious_action',
        ]);
        $this->assertTrue($responseStore->status() === 404 || $responseStore->status() === 405);

        // PUT (Update) -> should be 404 or 405
        $responseUpdate = $this->put('/admin/audit-logs/' . $log->id, [
            'action' => 'modified_action',
        ]);
        $this->assertTrue($responseUpdate->status() === 404 || $responseUpdate->status() === 405);

        // DELETE (Destroy) -> should be 404 or 405
        $responseDelete = $this->delete('/admin/audit-logs/' . $log->id);
        $this->assertTrue($responseDelete->status() === 404 || $responseDelete->status() === 405);
    }

    /** @test */
    public function non_super_admin_cannot_access_audit_logs()
    {
        $regularUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        
        $log = AuditLog::create([
            'actor_user_id' => $regularUser->id,
            'action' => 'user_deactivate',
            'entity_type' => 'users',
            'entity_id' => 15,
            'created_at' => now(),
        ]);

        // Index
        $this->actingAs($regularUser)->get(route('admin.audit-logs.index'))->assertStatus(403);

        // Show
        $this->actingAs($regularUser)->get(route('admin.audit-logs.show', $log->id))->assertStatus(403);
    }
}
