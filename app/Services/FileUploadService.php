<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    /**
     * Upload a vehicle photo to public storage.
     *
     * Validates format (JPG/PNG) and size (max 5MB) at the service level,
     * stores to storage/app/public/vehicles/, and returns the publicly
     * accessible URL path.
     *
     * @param  UploadedFile  $file  The uploaded image file.
     * @param  string  $directory  Storage sub-directory (default: 'vehicles').
     * @return string The publicly accessible URL path (e.g. /storage/vehicles/abc123.jpg).
     *
     * @throws \InvalidArgumentException If the file fails validation.
     */
    public function uploadVehiclePhoto(UploadedFile $file, string $directory = 'vehicles'): string
    {
        $this->validateImage($file);

        $path = $file->store($directory, 'public');

        return '/storage/' . $path;
    }

    /**
     * Delete a previously uploaded file from public storage.
     *
     * @param  string  $publicUrl  The public URL path (e.g. /storage/vehicles/abc123.jpg).
     * @return bool True if the file was deleted, false otherwise.
     */
    public function delete(string $publicUrl): bool
    {
        // Convert /storage/vehicles/abc.jpg → vehicles/abc.jpg
        $relativePath = str_replace('/storage/', '', $publicUrl);

        return Storage::disk('public')->delete($relativePath);
    }

    /**
     * Validate the uploaded image meets the constraints:
     * - Must be an image (JPG/PNG).
     * - Max size: 5MB (5120 KB).
     *
     * @param  UploadedFile  $file
     *
     * @throws \InvalidArgumentException
     */
    protected function validateImage(UploadedFile $file): void
    {
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSizeBytes = 5 * 1024 * 1024; // 5MB

        if (! in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException(
                'Format foto harus berupa JPEG, JPG, atau PNG.'
            );
        }

        if ($file->getSize() > $maxSizeBytes) {
            throw new \InvalidArgumentException(
                'Ukuran foto maksimal adalah 5MB.'
            );
        }
    }
}
