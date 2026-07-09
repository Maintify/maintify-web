<?php

namespace Tests\Feature\Workshop;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
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
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'operational_hours' => 'Senin - Sabtu (08:00 - 17:00)',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        return [$user, $workshop];
    }

    /** @test */
    public function approved_workshop_admin_can_view_profile_edit_page()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $response = $this->actingAs($admin)
            ->get(route('workshop.profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('workshop.profile.edit');
        $response->assertSee('Bengkel Utama');
        $response->assertSee('081200000001');
    }

    /** @test */
    public function approved_workshop_admin_can_update_profile_details()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $payload = [
            'name' => 'Bengkel Baru Jaya',
            'phone' => '081299999999',
            'email' => 'baru@bengkel.com',
            'address' => 'Jl. Baru No. 99',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40115',
            'description' => 'Bengkel serba bisa nomor satu',
            'operational_hours' => 'Setiap Hari (24 Jam)',
            'latitude' => '-6.9175',
            'longitude' => '107.6191',
        ];

        $response = $this->actingAs($admin)
            ->put(route('workshop.profile.update'), $payload);

        $response->assertRedirect(route('workshop.profile.edit'));
        $response->assertSessionHas('success');

        $workshop->refresh();

        $this->assertEquals('Bengkel Baru Jaya', $workshop->name);
        $this->assertEquals('081299999999', $workshop->phone);
        $this->assertEquals('baru@bengkel.com', $workshop->email);
        $this->assertEquals('Jl. Baru No. 99', $workshop->address);
        $this->assertEquals('Bandung', $workshop->city);
        $this->assertEquals('Jawa Barat', $workshop->province);
        $this->assertEquals('40115', $workshop->postal_code);
        $this->assertEquals('Bengkel serba bisa nomor satu', $workshop->description);
        $this->assertEquals('Setiap Hari (24 Jam)', $workshop->operational_hours);
        $this->assertEquals(-6.9175, $workshop->latitude);
        $this->assertEquals(107.6191, $workshop->longitude);
    }

    /** @test */
    public function profile_update_validation_errors()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $payload = [
            'name' => '', // required
            'phone' => '', // required
            'email' => 'not-an-email', // invalid email
            'address' => '', // required
            'city' => '', // required
            'province' => '', // required
            'operational_hours' => '', // required
            'latitude' => 'invalid-latitude', // numeric required
        ];

        $response = $this->actingAs($admin)
            ->put(route('workshop.profile.update'), $payload);

        $response->assertSessionHasErrors(['name', 'phone', 'email', 'address', 'city', 'province', 'operational_hours', 'latitude']);
    }

    /** @test */
    public function non_admin_workshop_users_cannot_access_profile_edit()
    {
        [$admin, $workshop] = $this->createApprovedWorkshopAdmin();

        $staffUser = User::factory()->create([
            'name' => 'Irfan Mekanik',
            'email' => 'irfan@bengkel.com',
            'role' => User::ROLE_WORKSHOP,
        ]);

        WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        // Irfan attempts to view profile edit page
        $response = $this->actingAs($staffUser)
            ->get(route('workshop.profile.edit'));
        $response->assertStatus(403);

        // Irfan attempts to update profile
        $response2 = $this->actingAs($staffUser)
            ->put(route('workshop.profile.update'), [
                'name' => 'Bengkel Diambil Alih',
                'phone' => '081299999999',
                'email' => 'takeover@bengkel.com',
                'address' => 'Jl. Take Over No. 1',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'operational_hours' => 'Setiap Hari (24 Jam)',
            ]);
        $response2->assertStatus(403);
    }

    /** @test */
    public function guests_cannot_access_profile_edit()
    {
        $response = $this->get(route('workshop.profile.edit'));
        $response->assertRedirect(route('login'));

        $response2 = $this->put(route('workshop.profile.update'), []);
        $response2->assertRedirect(route('login'));
    }
}
