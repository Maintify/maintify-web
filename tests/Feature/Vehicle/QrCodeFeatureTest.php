<?php

namespace Tests\Feature\Vehicle;

use App\Models\User;
use App\Models\Vehicle;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrCodeFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $otherUser;

    protected Vehicle $vehicle;

    protected QrCodeService $qrService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->owner = User::factory()->create(['role' => 'vehicle_owner']);
        $this->otherUser = User::factory()->create(['role' => 'vehicle_owner']);

        $this->vehicle = Vehicle::factory()->create(['user_id' => $this->owner->id]);

        $this->qrService = new QrCodeService;
    }

    public function test_guests_cannot_view_qr_code_page(): void
    {
        $response = $this->get(route('vehicles.qr.show', $this->vehicle));
        $response->assertRedirect(route('login'));
    }

    public function test_owner_can_view_qr_code_page(): void
    {
        // First ensure QR code is generated
        Storage::fake('public');
        $this->qrService->generateForVehicle($this->vehicle);

        $response = $this->actingAs($this->owner)
            ->get(route('vehicles.qr.show', $this->vehicle));

        $response->assertStatus(200);
        $response->assertViewIs('vehicles.qr-code');
        $response->assertSee('Digital ID QR Code');
        $response->assertSee($this->vehicle->plate_number);
        $response->assertSee('Verified / Aktif');
    }

    public function test_non_owner_cannot_view_qr_code_page(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->get(route('vehicles.qr.show', $this->vehicle));

        $response->assertStatus(403);
    }

    public function test_owner_can_download_qr_code(): void
    {
        Storage::fake('public');
        $this->qrService->generateForVehicle($this->vehicle);

        $response = $this->actingAs($this->owner)
            ->get(route('vehicles.qr.download', $this->vehicle));

        $response->assertStatus(200);

        // Assert it's a download response
        $this->assertTrue($response->headers->has('content-disposition'));
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('filename="QR_'.$this->vehicle->plate_number.'.svg"', $response->headers->get('content-disposition'));
    }

    public function test_download_redirects_back_if_no_qr_exists(): void
    {
        // Don't generate QR code
        $this->vehicle->update(['qr_code_url' => null]);

        $response = $this->actingAs($this->owner)
            ->get(route('vehicles.qr.download', $this->vehicle));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'QR Code belum tersedia.');
    }

    public function test_non_owner_cannot_download_qr_code(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->get(route('vehicles.qr.download', $this->vehicle));

        $response->assertStatus(403);
    }

    public function test_owner_can_regenerate_qr_code(): void
    {
        Storage::fake('public');

        // Generate initial QR code
        $this->qrService->generateForVehicle($this->vehicle);
        $oldToken = $this->vehicle->fresh()->qr_code;

        $response = $this->actingAs($this->owner)
            ->post(route('vehicles.qr.regenerate', $this->vehicle));

        $response->assertRedirect(route('vehicles.qr.show', $this->vehicle));
        $response->assertSessionHas('success');

        $this->vehicle->refresh();
        $this->assertNotEquals($oldToken, $this->vehicle->qr_code);

        // Assert old token is revoked in DB
        $this->assertDatabaseHas('qr_codes', [
            'vehicle_id' => $this->vehicle->id,
            'qr_token' => $oldToken,
            'status' => 'revoked',
        ]);

        // Assert new token is active
        $this->assertDatabaseHas('qr_codes', [
            'vehicle_id' => $this->vehicle->id,
            'qr_token' => $this->vehicle->qr_code,
            'status' => 'active',
        ]);
    }

    public function test_non_owner_cannot_regenerate_qr_code(): void
    {
        $response = $this->actingAs($this->otherUser)
            ->post(route('vehicles.qr.regenerate', $this->vehicle));

        $response->assertStatus(403);
    }
}
