<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function super_admin_can_view_pending_workshops_list()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        
        $pendingWorkshop = Workshop::create([
            'user_id' => User::factory()->create(['role' => User::ROLE_WORKSHOP])->id,
            'name' => 'Pending Garage',
            'phone' => '081234567890',
            'email' => 'pending@garage.com',
            'address' => 'Pending Road 12',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => false,
        ]);

        $approvedWorkshop = Workshop::create([
            'user_id' => User::factory()->create(['role' => User::ROLE_WORKSHOP])->id,
            'name' => 'Approved Garage',
            'phone' => '081234567891',
            'email' => 'approved@garage.com',
            'address' => 'Approved Road 12',
            'status' => Workshop::STATUS_APPROVED,
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.workshops.pending'));

        $response->assertStatus(200);
        $response->assertSee('Pending Garage');
        $response->assertDontSee('Approved Garage');
    }

    /** @test */
    public function super_admin_can_view_workshop_review_details()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        
        $workshop = Workshop::create([
            'user_id' => User::factory()->create(['role' => User::ROLE_WORKSHOP])->id,
            'name' => 'Signature Garage',
            'phone' => '081234567890',
            'email' => 'signature@garage.com',
            'address' => 'Golden Street 11',
            'owner_name' => 'Budi Santoso',
            'owner_ktp_number' => '1234567890123456',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => false,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.workshops.review', $workshop->id));

        $response->assertStatus(200);
        $response->assertSee('Signature Garage');
        $response->assertSee('Budi Santoso');
        $response->assertSee('1234567890123456');
    }

    /** @test */
    public function super_admin_can_approve_workshop()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        
        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Signature Garage',
            'phone' => '081234567890',
            'email' => 'signature@garage.com',
            'address' => 'Golden Street 11',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => false,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('admin.workshops.approve', $workshop->id));

        $response->assertRedirect(route('admin.workshops.pending'));

        // Assert workshop status and activity flag
        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_APPROVED, $workshop->status);
        $this->assertTrue($workshop->is_active);
        $this->assertEquals($superAdmin->id, $workshop->approved_by);
        $this->assertNotNull($workshop->approved_at);

        // Assert WorkshopVerification record was created
        $this->assertDatabaseHas('workshop_verifications', [
            'workshop_id' => $workshop->id,
            'reviewed_by' => $superAdmin->id,
            'status' => WorkshopVerification::STATUS_APPROVED,
        ]);

        // Assert AuditLog entry
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'verify_workshop_approve',
            'entity_type' => 'workshops',
            'entity_id' => $workshop->id,
        ]);

        // Assert Notification sent to workshop owner
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'workshop_verification_approved',
            'title' => 'Pendaftaran Bengkel Disetujui',
        ]);
    }

    /** @test */
    public function super_admin_can_reject_workshop_with_reason()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        
        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Signature Garage',
            'phone' => '081234567890',
            'email' => 'signature@garage.com',
            'address' => 'Golden Street 11',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => false,
        ]);

        $reason = 'Dokumen legalitas tidak valid.';
        $response = $this->actingAs($superAdmin)->post(route('admin.workshops.reject', $workshop->id), [
            'rejection_reason' => $reason,
        ]);

        $response->assertRedirect(route('admin.workshops.pending'));

        // Assert workshop status and rejection reason
        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_REJECTED, $workshop->status);
        $this->assertEquals($reason, $workshop->rejection_reason);
        $this->assertFalse($workshop->is_active);

        // Assert WorkshopVerification log
        $this->assertDatabaseHas('workshop_verifications', [
            'workshop_id' => $workshop->id,
            'reviewed_by' => $superAdmin->id,
            'status' => WorkshopVerification::STATUS_REJECTED,
            'rejection_reason' => $reason,
        ]);

        // Assert AuditLog entry
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'verify_workshop_reject',
            'entity_type' => 'workshops',
            'entity_id' => $workshop->id,
        ]);

        // Assert Notification sent
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'workshop_verification_rejected',
            'title' => 'Pendaftaran Bengkel Ditolak',
        ]);
    }

    /** @test */
    public function super_admin_cannot_reject_without_reason()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        
        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Signature Garage',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => false,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('admin.workshops.reject', $workshop->id), [
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors(['rejection_reason']);

        // Assert status did not change
        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_PENDING, $workshop->status);
    }

    /** @test */
    public function super_admin_can_request_revision_with_reason()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        
        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Signature Garage',
            'phone' => '081234567890',
            'email' => 'signature@garage.com',
            'address' => 'Golden Street 11',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => false,
        ]);

        $reason = 'Lengkapi foto KTP pemilik.';
        $response = $this->actingAs($superAdmin)->post(route('admin.workshops.revision', $workshop->id), [
            'rejection_reason' => $reason,
        ]);

        $response->assertRedirect(route('admin.workshops.pending'));

        // Assert workshop status and revision reason
        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_REVISION_NEEDED, $workshop->status);
        $this->assertEquals($reason, $workshop->rejection_reason);
        $this->assertFalse($workshop->is_active);

        // Assert WorkshopVerification log
        $this->assertDatabaseHas('workshop_verifications', [
            'workshop_id' => $workshop->id,
            'reviewed_by' => $superAdmin->id,
            'status' => WorkshopVerification::STATUS_REVISION_NEEDED,
            'rejection_reason' => $reason,
        ]);

        // Assert AuditLog entry
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'verify_workshop_revision',
            'entity_type' => 'workshops',
            'entity_id' => $workshop->id,
        ]);

        // Assert Notification sent
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'workshop_verification_revision_needed',
            'title' => 'Revisi Pendaftaran Bengkel Diperlukan',
        ]);
    }

    /** @test */
    public function non_super_admin_cannot_access_verification_endpoints()
    {
        $regularUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        
        $workshop = Workshop::create([
            'user_id' => User::factory()->create(['role' => User::ROLE_WORKSHOP])->id,
            'name' => 'Signature Garage',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => false,
        ]);

        // Try viewing pending queue
        $this->actingAs($regularUser)->get(route('admin.workshops.pending'))->assertStatus(403);

        // Try viewing review details
        $this->actingAs($regularUser)->get(route('admin.workshops.review', $workshop->id))->assertStatus(403);

        // Try approving
        $this->actingAs($regularUser)->post(route('admin.workshops.approve', $workshop->id))->assertStatus(403);

        // Try rejecting
        $this->actingAs($regularUser)->post(route('admin.workshops.reject', $workshop->id), ['rejection_reason' => 'reason'])->assertStatus(403);

        // Try requesting revision
        $this->actingAs($regularUser)->post(route('admin.workshops.revision', $workshop->id), ['rejection_reason' => 'reason'])->assertStatus(403);
    }
}
