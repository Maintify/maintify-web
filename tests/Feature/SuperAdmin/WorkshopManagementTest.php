<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function super_admin_can_view_workshops_list()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $wsUser1 = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $wsUser2 = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop1 = Workshop::create([
            'user_id' => $wsUser1->id,
            'name' => 'Signature Tuning',
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $workshop2 = Workshop::create([
            'user_id' => $wsUser2->id,
            'name' => 'Elite Repair',
            'status' => Workshop::STATUS_PENDING,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.workshops.index'));

        $response->assertStatus(200);
        $response->assertSee('Signature Tuning');
        $response->assertSee('Elite Repair');
    }

    /** @test */
    public function super_admin_can_filter_workshops_by_status()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $wsUser1 = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $wsUser2 = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop1 = Workshop::create([
            'user_id' => $wsUser1->id,
            'name' => 'Signature Tuning',
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $workshop2 = Workshop::create([
            'user_id' => $wsUser2->id,
            'name' => 'Elite Repair',
            'status' => Workshop::STATUS_PENDING,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.workshops.index', ['status' => Workshop::STATUS_APPROVED]));

        $response->assertStatus(200);
        $response->assertSee('Signature Tuning');
        $response->assertDontSee('Elite Repair');
    }

    /** @test */
    public function super_admin_can_search_workshops_by_name_city_or_email()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $wsUser1 = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $wsUser2 = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop1 = Workshop::create([
            'user_id' => $wsUser1->id,
            'name' => 'Signature Tuning',
            'city' => 'Bandung',
            'email' => 'signature@tuning.com',
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $workshop2 = Workshop::create([
            'user_id' => $wsUser2->id,
            'name' => 'Elite Repair',
            'city' => 'Jakarta',
            'email' => 'elite@repair.com',
            'status' => Workshop::STATUS_APPROVED,
        ]);

        // Search by Name
        $response = $this->actingAs($superAdmin)->get(route('admin.workshops.index', ['search' => 'Signature']));
        $response->assertStatus(200);
        $response->assertSee('Signature Tuning');
        $response->assertDontSee('Elite Repair');

        // Search by City
        $response = $this->actingAs($superAdmin)->get(route('admin.workshops.index', ['search' => 'Jakarta']));
        $response->assertStatus(200);
        $response->assertSee('Elite Repair');
        $response->assertDontSee('Signature Tuning');
    }

    /** @test */
    public function super_admin_can_view_workshop_details()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $wsUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $wsUser->id,
            'name' => 'Signature Tuning',
            'owner_name' => 'John Doe',
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $staffUser = User::factory()->create(['name' => 'Jane Mechanic']);
        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get(route('admin.workshops.show', $workshop->id));

        $response->assertStatus(200);
        $response->assertSee('Signature Tuning');
        $response->assertSee('John Doe');
        $response->assertSee('Jane Mechanic');
    }

    /** @test */
    public function super_admin_can_update_workshop_status_to_rejected_with_reason()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $wsUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $wsUser->id,
            'name' => 'Signature Tuning',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->put(route('admin.workshops.update', $workshop->id), [
            'status' => Workshop::STATUS_REJECTED,
            'rejection_reason' => 'Dokumen legalitas KTP kabur.',
            'is_active' => 0,
        ]);

        $response->assertRedirect();

        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_REJECTED, $workshop->status);
        $this->assertEquals('Dokumen legalitas KTP kabur.', $workshop->rejection_reason);
        $this->assertFalse($workshop->is_active);

        // Assert audit log recorded
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'workshop_status_update',
            'entity_type' => 'workshops',
            'entity_id' => $workshop->id,
        ]);
    }

    /** @test */
    public function super_admin_can_approve_workshop_directly()
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $wsUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $wsUser->id,
            'name' => 'Signature Tuning',
            'status' => Workshop::STATUS_PENDING,
            'is_active' => false,
        ]);

        $response = $this->actingAs($superAdmin)->put(route('admin.workshops.update', $workshop->id), [
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $response->assertRedirect();

        $workshop->refresh();
        $this->assertEquals(Workshop::STATUS_APPROVED, $workshop->status);
        $this->assertTrue($workshop->is_active);
        $this->assertNotNull($workshop->approved_at);
        $this->assertEquals($superAdmin->id, $workshop->approved_by);
    }

    /** @test */
    public function non_super_admin_cannot_access_workshop_management()
    {
        $regularUser = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $wsUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $wsUser->id,
            'name' => 'Signature Tuning',
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $this->actingAs($regularUser)->get(route('admin.workshops.index'))->assertStatus(403);
        $this->actingAs($regularUser)->get(route('admin.workshops.show', $workshop->id))->assertStatus(403);
        $this->actingAs($regularUser)->put(route('admin.workshops.update', $workshop->id), [
            'status' => Workshop::STATUS_APPROVED,
        ])->assertStatus(403);
    }
}
