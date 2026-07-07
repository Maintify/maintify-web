<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\AuditLog;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function super_admin_can_access_dashboard_with_all_metrics()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // Create some sample data for metrics
        User::factory()->count(3)->create(['role' => User::ROLE_VEHICLE_OWNER]);
        User::factory()->count(2)->create(['role' => User::ROLE_WORKSHOP]);

        $response = $this->actingAs($superAdmin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalUsers');
        $response->assertViewHas('totalVehicles');
        $response->assertViewHas('totalWorkshops');
        $response->assertViewHas('totalServiceRecords');
        $response->assertViewHas('chartLabels');
        $response->assertViewHas('chartValues');
        $response->assertViewHas('systemHealth');
        $response->assertViewHas('pendingWorkshops');
    }

    /** @test */
    public function non_super_admin_cannot_access_super_admin_actions()
    {
        $customer = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Pending Garage',
            'status' => Workshop::STATUS_PENDING,
        ]);

        // Try as customer
        $responseApproveCustomer = $this->actingAs($customer)->post(route('admin.workshops.approve', $workshop->id));
        $responseApproveCustomer->assertStatus(403);

        // Try as workshop owner
        $responseApproveOwner = $this->actingAs($owner)->post(route('admin.workshops.approve', $workshop->id));
        $responseApproveOwner->assertStatus(403);
    }

    /** @test */
    public function super_admin_can_approve_pending_workshop()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Signature Garage',
            'status' => Workshop::STATUS_PENDING,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('admin.workshops.approve', $workshop->id));

        $response->assertRedirect();

        // Assert workshop status changed to approved
        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_APPROVED, $workshop->status);
        $this->assertEquals($superAdmin->id, $workshop->approved_by);
        $this->assertNotNull($workshop->approved_at);

        // Assert WorkshopVerification record is created
        $this->assertDatabaseHas('workshop_verifications', [
            'workshop_id' => $workshop->id,
            'reviewed_by' => $superAdmin->id,
            'status' => WorkshopVerification::STATUS_APPROVED,
        ]);

        // Assert AuditLog entry is recorded
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'verify_workshop_approve',
            'entity_type' => 'workshops',
            'entity_id' => $workshop->id,
        ]);
    }

    /** @test */
    public function super_admin_can_reject_pending_workshop_with_reason()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Signature Garage',
            'status' => Workshop::STATUS_PENDING,
        ]);

        $rejectionReason = 'Dokumen legalitas tidak lengkap.';
        $response = $this->actingAs($superAdmin)->post(route('admin.workshops.reject', $workshop->id), [
            'rejection_reason' => $rejectionReason,
        ]);

        $response->assertRedirect();

        // Assert workshop status changed to rejected
        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_REJECTED, $workshop->status);
        $this->assertEquals($rejectionReason, $workshop->rejection_reason);

        // Assert WorkshopVerification record is created
        $this->assertDatabaseHas('workshop_verifications', [
            'workshop_id' => $workshop->id,
            'reviewed_by' => $superAdmin->id,
            'status' => WorkshopVerification::STATUS_REJECTED,
            'rejection_reason' => $rejectionReason,
        ]);

        // Assert AuditLog entry is recorded
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'verify_workshop_reject',
            'entity_type' => 'workshops',
            'entity_id' => $workshop->id,
        ]);
    }

    /** @test */
    public function super_admin_cannot_reject_without_providing_reason()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Signature Garage',
            'status' => Workshop::STATUS_PENDING,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('admin.workshops.reject', $workshop->id), [
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors(['rejection_reason']);

        // Assert status did not change
        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_PENDING, $workshop->status);
    }
}
