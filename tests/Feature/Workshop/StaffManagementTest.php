<?php

namespace Tests\Feature\Workshop;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createApprovedWorkshopAdmin(): array
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $workshop = Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Utama',
            'phone' => '081200000001',
            'email' => 'utama@bengkel.com',
            'address' => 'Jl. Utama No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        return [$user, $workshop];
    }

    /** @test */
    public function approved_workshop_admin_can_view_staff_list()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create([
            'name' => 'Ahmad Mekanik',
            'email' => 'ahmad@bengkel.com',
            'phone_number' => '081222222222',
            'role' => User::ROLE_WORKSHOP,
        ]);

        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('workshop.staff.index'));

        $response->assertStatus(200);
        $response->assertViewIs('workshop.staff.index');
        $response->assertSee('Ahmad Mekanik');
        $response->assertSee('ahmad@bengkel.com');
    }

    /** @test */
    public function approved_workshop_admin_can_search_staff()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser1 = User::factory()->create([
            'name' => 'Ahmad Mekanik',
            'email' => 'ahmad@bengkel.com',
            'role' => User::ROLE_WORKSHOP,
        ]);
        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser1->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
        ]);

        $staffUser2 = User::factory()->create([
            'name' => 'Budi Kasir',
            'email' => 'budi@bengkel.com',
            'role' => User::ROLE_WORKSHOP,
        ]);
        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser2->id,
            'position' => WorkshopStaff::POSITION_ADMIN,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('workshop.staff.index', ['search' => 'Ahmad']));

        $response->assertSee('Ahmad Mekanik');
        $response->assertDontSee('Budi Kasir');
    }

    /** @test */
    public function approved_workshop_admin_can_create_staff()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $payload = [
            'name' => 'Candra Mekanik',
            'email' => 'candra@bengkel.com',
            'phone_number' => '081233333333',
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($admin)
            ->post(route('workshop.staff.store'), $payload);

        $response->assertRedirect(route('workshop.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Candra Mekanik',
            'email' => 'candra@bengkel.com',
            'phone_number' => '081233333333',
            'role' => User::ROLE_WORKSHOP,
        ]);

        $user = User::where('email', 'candra@bengkel.com')->first();

        $this->assertDatabaseHas('workshop_staff', [
            'workshop_id' => $workshop->id,
            'user_id' => $user->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function approved_workshop_admin_can_edit_and_update_staff()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create([
            'name' => 'Doni Mekanik',
            'email' => 'doni@bengkel.com',
            'phone_number' => '081244444444',
            'role' => User::ROLE_WORKSHOP,
        ]);

        $staff = WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        $payload = [
            'name' => 'Doni Spv',
            'email' => 'doni_spv@bengkel.com',
            'phone_number' => '081255555555',
            'position' => WorkshopStaff::POSITION_ADMIN,
            'is_active' => '1',
        ];

        $response = $this->actingAs($admin)
            ->put(route('workshop.staff.update', $staff), $payload);

        $response->assertRedirect(route('workshop.staff.index'));
        $response->assertSessionHas('success');

        $staffUser->refresh();
        $staff->refresh();

        $this->assertEquals('Doni Spv', $staffUser->name);
        $this->assertEquals('doni_spv@bengkel.com', $staffUser->email);
        $this->assertEquals('081255555555', $staffUser->phone_number);
        $this->assertEquals(WorkshopStaff::POSITION_ADMIN, $staff->position);
        $this->assertTrue($staff->is_active);
    }

    /** @test */
    public function approved_workshop_admin_can_deactivate_staff()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create([
            'name' => 'Eka Mekanik',
            'email' => 'eka@bengkel.com',
            'role' => User::ROLE_WORKSHOP,
        ]);

        $staff = WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        // Payload with is_active omitted/unchecked
        $payload = [
            'name' => 'Eka Mekanik',
            'email' => 'eka@bengkel.com',
            'phone_number' => '081266666666',
            'position' => WorkshopStaff::POSITION_MECHANIC,
        ];

        $response = $this->actingAs($admin)
            ->put(route('workshop.staff.update', $staff), $payload);

        $response->assertRedirect(route('workshop.staff.index'));

        $staff->refresh();
        $this->assertFalse($staff->is_active);
    }

    /** @test */
    public function approved_workshop_admin_can_delete_staff()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create([
            'name' => 'Fajar Mekanik',
            'email' => 'fajar@bengkel.com',
            'role' => User::ROLE_WORKSHOP,
        ]);

        $staff = WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('workshop.staff.destroy', $staff));

        $response->assertRedirect(route('workshop.staff.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $staffUser->id,
        ]);

        $this->assertDatabaseMissing('workshop_staff', [
            'id' => $staff->id,
        ]);
    }

    /** @test */
    public function deactivated_staff_cannot_login()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create([
            'name' => 'Gita Mekanik',
            'email' => 'gita@bengkel.com',
            'password' => Hash::make('password123'),
            'role' => User::ROLE_WORKSHOP,
        ]);

        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => false, // Deactivated
        ]);

        $response = $this->post(route('login'), [
            'email' => 'gita@bengkel.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function deactivated_staff_session_is_terminated_by_middleware()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create([
            'name' => 'Hadi Mekanik',
            'email' => 'hadi@bengkel.com',
            'role' => User::ROLE_WORKSHOP,
        ]);

        $staff = WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        // Login first while active
        $this->actingAs($staffUser);

        // Deactivate staff in background
        $staff->update(['is_active' => false]);

        // Attempt to access any workshop page
        $response = $this->get(route('dashboard'));

        // Middleware logs out and redirects
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function non_admin_workshop_users_cannot_access_staff_crud()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create([
            'name' => 'Irfan Mekanik',
            'email' => 'irfan@bengkel.com',
            'role' => User::ROLE_WORKSHOP,
        ]);

        $staff = WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        // Irfan attempts to access staff list
        $response = $this->actingAs($staffUser)
            ->get(route('workshop.staff.index'));

        $response->assertStatus(403);

        // Irfan attempts to edit staff
        $response2 = $this->actingAs($staffUser)
            ->get(route('workshop.staff.edit', $staff));

        $response2->assertStatus(403);
    }
}
