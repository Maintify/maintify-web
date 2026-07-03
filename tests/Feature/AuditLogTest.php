<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_audit_log_with_correct_relations()
    {
        // 1. Create user
        $actor = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // 2. Create AuditLog
        $log = AuditLog::create([
            'actor_user_id' => $actor->id,
            'action' => 'workshop.verified',
            'entity_type' => 'Workshop',
            'entity_id' => 1,
            'metadata' => ['status' => 'approved', 'reason' => null],
            'ip_address' => '192.168.1.1',
        ]);

        // 3. Assertions — database
        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'actor_user_id' => $actor->id,
            'action' => 'workshop.verified',
            'entity_type' => 'Workshop',
            'entity_id' => 1,
            'ip_address' => '192.168.1.1',
        ]);

        // 4. Assert metadata is stored as JSON and retrieved as array
        $freshLog = AuditLog::find($log->id);
        $this->assertIsArray($freshLog->metadata);
        $this->assertEquals('approved', $freshLog->metadata['status']);

        // 5. Assert relationship
        $this->assertEquals($actor->id, $freshLog->actor->id);

        // 6. Assert inverse relationship
        $this->assertTrue($actor->auditLogs->contains($freshLog));
    }

    /** @test */
    public function it_has_no_updated_at_column()
    {
        $actor = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $log = AuditLog::create([
            'actor_user_id' => $actor->id,
            'action' => 'user.login',
            'ip_address' => '10.0.0.1',
        ]);

        // Append-only: no updated_at
        $this->assertNull($log->updated_at ?? null);
        $this->assertFalse($log->timestamps);
    }

    /** @test */
    public function it_allows_nullable_actor_for_system_actions()
    {
        $log = AuditLog::create([
            'actor_user_id' => null,
            'action' => 'system.cron.cleanup',
            'entity_type' => null,
            'entity_id' => null,
            'metadata' => ['records_deleted' => 42],
            'ip_address' => null,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'actor_user_id' => null,
            'action' => 'system.cron.cleanup',
        ]);

        $this->assertNull($log->actor);
    }
}
