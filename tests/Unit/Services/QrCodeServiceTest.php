<?php

namespace Tests\Unit\Services;

use App\Models\QrCode as QrCodeModel;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QrCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QrCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QrCodeService();
    }

    // =========================================================
    // Token Generation
    // =========================================================

    public function test_generate_token_returns_unique_formatted_string(): void
    {
        $token = $this->service->generateToken();

        // Token must start with MNT- prefix
        $this->assertStringStartsWith('MNT-', $token);

        // Token format: MNT-{16 hex chars}-{8 hex chars}
        $this->assertMatchesRegularExpression('/^MNT-[A-F0-9]{16}-[A-F0-9]{8}$/', $token);
    }

    public function test_generated_tokens_are_unique(): void
    {
        $tokens = [];
        for ($i = 0; $i < 50; $i++) {
            $tokens[] = $this->service->generateToken();
        }

        // All 50 tokens should be unique
        $this->assertCount(50, array_unique($tokens));
    }

    public function test_token_uses_cryptographic_randomness(): void
    {
        // Generate two tokens — they should differ (extremely high probability)
        $token1 = $this->service->generateToken();
        $token2 = $this->service->generateToken();

        $this->assertNotEquals($token1, $token2);
    }

    // =========================================================
    // Resolve URL
    // =========================================================

    public function test_build_resolve_url_contains_token(): void
    {
        $token = 'MNT-ABCDEF0123456789-01234567';
        $url = $this->service->buildResolveUrl($token);

        $this->assertStringContainsString('/qr/resolve/', $url);
        $this->assertStringContainsString($token, $url);
    }

    // =========================================================
    // QR Image Generation
    // =========================================================

    public function test_generate_qr_image_returns_svg_content(): void
    {
        $data = 'https://example.com/qr/resolve/TEST-TOKEN';
        $svgContent = $this->service->generateQrImage($data);

        // Should be a valid SVG string
        $this->assertNotEmpty($svgContent);
        $this->assertStringContainsString('<svg', $svgContent);
        $this->assertStringContainsString('</svg>', $svgContent);
    }

    // =========================================================
    // Generate and Store
    // =========================================================

    public function test_generate_and_store_saves_file_to_disk(): void
    {
        Storage::fake('public');

        $data = 'https://example.com/qr/resolve/TEST-TOKEN';
        $filename = 'test-qr-code';

        $url = $this->service->generateAndStore($data, $filename);

        $this->assertEquals('/storage/qrcodes/test-qr-code.svg', $url);
        Storage::disk('public')->assertExists('qrcodes/test-qr-code.svg');
    }

    // =========================================================
    // Generate for Vehicle
    // =========================================================

    public function test_generate_for_vehicle_creates_qr_code_record(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $qrCode = $this->service->generateForVehicle($vehicle);

        // QR Code record should be created
        $this->assertInstanceOf(QrCodeModel::class, $qrCode);
        $this->assertEquals($vehicle->id, $qrCode->vehicle_id);
        $this->assertEquals(QrCodeModel::STATUS_ACTIVE, $qrCode->status);
        $this->assertNotNull($qrCode->issued_at);

        // Token must follow our format
        $this->assertMatchesRegularExpression('/^MNT-[A-F0-9]{16}-[A-F0-9]{8}$/', $qrCode->qr_token);

        // Vehicle should be updated with qr_code and qr_code_url
        $vehicle->refresh();
        $this->assertEquals($qrCode->qr_token, $vehicle->qr_code);
        $this->assertNotNull($vehicle->qr_code_url);
    }

    public function test_generate_for_vehicle_stores_qr_image_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $this->service->generateForVehicle($vehicle);

        // Assert QR code image file was stored
        $vehicle->refresh();
        $relativePath = str_replace('/storage/', '', $vehicle->qr_code_url);
        Storage::disk('public')->assertExists($relativePath);
    }

    // =========================================================
    // Regenerate for Vehicle
    // =========================================================

    public function test_regenerate_for_vehicle_revokes_old_and_creates_new(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        // Generate initial QR code
        $oldQrCode = $this->service->generateForVehicle($vehicle);
        $oldToken = $oldQrCode->qr_token;

        // Regenerate
        $newQrCode = $this->service->regenerateForVehicle($vehicle);

        // Old QR code should be revoked
        $oldQrCode->refresh();
        $this->assertEquals(QrCodeModel::STATUS_REVOKED, $oldQrCode->status);
        $this->assertNotNull($oldQrCode->revoked_at);

        // New QR code should be active with a different token
        $this->assertEquals(QrCodeModel::STATUS_ACTIVE, $newQrCode->status);
        $this->assertNotEquals($oldToken, $newQrCode->qr_token);
    }

    // =========================================================
    // Resolve Token
    // =========================================================

    public function test_resolve_token_returns_vehicle_for_active_token(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $qrCode = $this->service->generateForVehicle($vehicle);

        $resolved = $this->service->resolveToken($qrCode->qr_token);

        $this->assertNotNull($resolved);
        $this->assertEquals($vehicle->id, $resolved->id);
    }

    public function test_resolve_token_returns_null_for_revoked_token(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $vehicle = Vehicle::factory()->create(['user_id' => $user->id]);

        $qrCode = $this->service->generateForVehicle($vehicle);
        $token = $qrCode->qr_token;

        // Regenerate (which revokes the old token)
        $this->service->regenerateForVehicle($vehicle);

        $resolved = $this->service->resolveToken($token);

        $this->assertNull($resolved);
    }

    public function test_resolve_token_returns_null_for_nonexistent_token(): void
    {
        $resolved = $this->service->resolveToken('NONEXISTENT-TOKEN-123');

        $this->assertNull($resolved);
    }
}
