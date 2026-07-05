<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopPendingApprovalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: buat user workshop beserta data bengkel dengan status tertentu.
     */
    private function createWorkshopUser(string $status = 'pending', ?string $rejectionReason = null): User
    {
        $user = User::factory()->create([
            'role' => User::ROLE_WORKSHOP,
        ]);

        Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Test',
            'phone' => '081234567890',
            'email' => $user->email,
            'address' => 'Jl. Test No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'owner_name' => $user->name,
            'owner_ktp_number' => '1234567890123456',
            'operational_hours' => 'Senin - Jumat: 08:00 - 17:00',
            'is_active' => $status === 'approved',
            'status' => $status,
            'rejection_reason' => $rejectionReason,
        ]);

        return $user;
    }

    // =============================================================
    // Halaman Pending Rendering
    // =============================================================

    /**
     * Test: Workshop user dengan status pending melihat halaman menunggu verifikasi.
     */
    public function test_pending_workshop_sees_pending_page(): void
    {
        $user = $this->createWorkshopUser('pending');

        $response = $this->actingAs($user)->get(route('workshop.pending'));

        $response->assertStatus(200);
        $response->assertSee('Menunggu Verifikasi');
        $response->assertSee('Bengkel Test');
        $response->assertSee('Verifikasi Admin');
    }

    /**
     * Test: Workshop user dengan status rejected melihat alasan penolakan.
     */
    public function test_rejected_workshop_sees_rejection_reason(): void
    {
        $user = $this->createWorkshopUser('rejected', 'Dokumen legalitas tidak valid');

        $response = $this->actingAs($user)->get(route('workshop.pending'));

        $response->assertStatus(200);
        $response->assertSee('Pendaftaran Ditolak');
        $response->assertSee('Dokumen legalitas tidak valid');
        $response->assertSee('Alasan Penolakan');
    }

    /**
     * Test: Workshop user dengan status revision_needed melihat catatan admin.
     */
    public function test_revision_needed_workshop_sees_revision_notes(): void
    {
        $user = $this->createWorkshopUser('revision_needed', 'Mohon upload ulang foto KTP yang lebih jelas');

        $response = $this->actingAs($user)->get(route('workshop.pending'));

        $response->assertStatus(200);
        $response->assertSee('Revisi Diperlukan');
        $response->assertSee('Mohon upload ulang foto KTP yang lebih jelas');
        $response->assertSee('Catatan dari Admin');
    }

    /**
     * Test: Workshop user yang sudah approved di-redirect ke dashboard.
     */
    public function test_approved_workshop_redirects_to_dashboard(): void
    {
        $user = $this->createWorkshopUser('approved');

        $response = $this->actingAs($user)->get(route('workshop.pending'));

        $response->assertRedirect(route('dashboard'));
    }

    // =============================================================
    // Middleware: WorkshopApprovedMiddleware
    // =============================================================

    /**
     * Test: Middleware memblokir akses dashboard untuk workshop yang belum approved.
     */
    public function test_middleware_blocks_pending_workshop_from_dashboard(): void
    {
        $user = $this->createWorkshopUser('pending');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('workshop.pending'));
    }

    /**
     * Test: Middleware memblokir akses dashboard untuk workshop yang ditolak.
     */
    public function test_middleware_blocks_rejected_workshop_from_dashboard(): void
    {
        $user = $this->createWorkshopUser('rejected', 'Ditolak oleh admin');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('workshop.pending'));
    }

    /**
     * Test: Middleware memblokir akses dashboard untuk workshop yang perlu revisi.
     */
    public function test_middleware_blocks_revision_needed_workshop_from_dashboard(): void
    {
        $user = $this->createWorkshopUser('revision_needed', 'Perlu perbaikan');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('workshop.pending'));
    }

    /**
     * Test: Workshop yang approved bisa akses dashboard.
     */
    public function test_approved_workshop_can_access_dashboard(): void
    {
        $user = $this->createWorkshopUser('approved');

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test: Vehicle owner tidak terpengaruh oleh middleware workshop.approved.
     */
    public function test_vehicle_owner_not_affected_by_workshop_middleware(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test: Super admin tidak terpengaruh oleh middleware workshop.approved.
     */
    public function test_super_admin_not_affected_by_workshop_middleware(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /**
     * Test: Guest tidak bisa mengakses halaman pending (redirect ke login).
     */
    public function test_guest_cannot_access_pending_page(): void
    {
        $response = $this->get(route('workshop.pending'));

        $response->assertRedirect(route('login'));
    }
}
