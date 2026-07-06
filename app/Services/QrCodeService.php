<?php

namespace App\Services;

use App\Models\QrCode as QrCodeModel;
use App\Models\Vehicle;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeService
{
    /**
     * Generate a unique, encrypted QR token.
     *
     * The token is composed of a timestamp-based prefix and a random suffix
     * to ensure uniqueness and prevent sequential guessing (SEC-006).
     *
     * Format: MNT-{random_hex(16)}-{random_hex(8)}
     *
     * @return string The generated unique QR token.
     */
    public function generateToken(): string
    {
        do {
            $token = 'MNT-'.strtoupper(bin2hex(random_bytes(8))).'-'.strtoupper(bin2hex(random_bytes(4)));
        } while (QrCodeModel::where('qr_token', $token)->exists());

        return $token;
    }

    /**
     * Build the full resolve URL that the QR Code will encode.
     *
     * @param  string  $token  The unique QR token.
     * @return string The URL encoded in the QR Code image.
     */
    public function buildResolveUrl(string $token): string
    {
        return url("/qr/resolve/{$token}");
    }

    /**
     * Generate a QR Code PNG image for the given data string.
     *
     * Returns the raw PNG binary content.
     *
     * @param  string  $data  The data string to encode in the QR code (typically a URL).
     * @return string Raw PNG image binary.
     */
    public function generateQrImage(string $data): string
    {
        $options = new QROptions([
            'outputType' => QRMarkupSVG::class,
            'eccLevel' => EccLevel::H,
            'scale' => 10,
            'outputBase64' => false,
            'svgViewBoxSize' => null,
            'addQuietzone' => true,
            'quietzoneSize' => 2,
            'markupDark' => '#1A1C1C',
            'markupLight' => '#FFFFFF',
        ]);

        $qrcode = new QRCode($options);

        return $qrcode->render($data);
    }

    /**
     * Generate a QR Code image and store it to disk as PNG.
     *
     * @param  string  $data  The data to encode.
     * @param  string  $filename  The filename (without extension).
     * @return string The publicly accessible URL path of the stored image.
     */
    public function generateAndStore(string $data, string $filename): string
    {
        $svgContent = $this->generateQrImage($data);

        $path = "qrcodes/{$filename}.svg";
        Storage::disk('public')->put($path, $svgContent);

        return '/storage/'.$path;
    }

    /**
     * Generate a QR Code for a vehicle.
     *
     * Creates a unique token, stores it in the qr_codes table,
     * generates the QR Code image, and stores it.
     *
     * @param  Vehicle  $vehicle  The vehicle to generate a QR Code for.
     * @return QrCodeModel The newly created QR Code record.
     */
    public function generateForVehicle(Vehicle $vehicle): QrCodeModel
    {
        $token = $this->generateToken();
        $resolveUrl = $this->buildResolveUrl($token);

        // Generate and store the QR Code image
        $filename = "vehicle-{$vehicle->id}-".Str::lower(Str::random(8));
        $imageUrl = $this->generateAndStore($resolveUrl, $filename);

        // Update vehicle's qr_code and qr_code_url fields
        $vehicle->update([
            'qr_code' => $token,
            'qr_code_url' => $imageUrl,
        ]);

        // Create QR Code record in the database
        $qrCode = $vehicle->qrCodes()->create([
            'qr_token' => $token,
            'status' => QrCodeModel::STATUS_ACTIVE,
            'issued_at' => now(),
        ]);

        return $qrCode;
    }

    /**
     * Regenerate the QR Code for a vehicle.
     *
     * Revokes all existing active QR codes for the vehicle,
     * then generates a new one.
     *
     * @param  Vehicle  $vehicle  The vehicle to regenerate QR Code for.
     * @return QrCodeModel The newly created QR Code record.
     */
    public function regenerateForVehicle(Vehicle $vehicle): QrCodeModel
    {
        // Revoke all existing active QR codes for this vehicle
        $vehicle->qrCodes()
            ->where('status', QrCodeModel::STATUS_ACTIVE)
            ->update([
                'status' => QrCodeModel::STATUS_REVOKED,
                'revoked_at' => now(),
            ]);

        // Delete old QR code image if exists
        if ($vehicle->qr_code_url) {
            $relativePath = str_replace('/storage/', '', $vehicle->qr_code_url);
            Storage::disk('public')->delete($relativePath);
        }

        return $this->generateForVehicle($vehicle);
    }

    /**
     * Resolve a QR token to its vehicle.
     *
     * Finds the active QR Code record by token and returns the associated vehicle.
     *
     * @param  string  $token  The QR token to resolve.
     * @return Vehicle|null The associated vehicle, or null if not found/inactive.
     */
    public function resolveToken(string $token): ?Vehicle
    {
        $qrCode = QrCodeModel::where('qr_token', $token)
            ->where('status', QrCodeModel::STATUS_ACTIVE)
            ->first();

        if (! $qrCode) {
            return null;
        }

        return $qrCode->vehicle;
    }
}
