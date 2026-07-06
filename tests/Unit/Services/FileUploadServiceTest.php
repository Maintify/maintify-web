<?php

namespace Tests\Unit\Services;

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadServiceTest extends TestCase
{
    protected FileUploadService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FileUploadService;
        Storage::fake('public');
    }

    /** @test */
    public function it_can_upload_a_valid_vehicle_photo()
    {
        // 1. Create a fake uploaded file (100 KB, JPG)
        $file = UploadedFile::fake()->image('vehicle_photo.jpg', 800, 600)->size(100);

        // 2. Call the upload method
        $publicUrl = $this->service->uploadVehiclePhoto($file);

        // 3. Assert the returned URL pattern is correct
        $this->assertStringStartsWith('/storage/vehicles/', $publicUrl);

        // 4. Assert file exists on disk
        $relativePath = str_replace('/storage/', '', $publicUrl);
        Storage::disk('public')->assertExists($relativePath);
    }

    /** @test */
    public function it_throws_exception_if_file_format_is_invalid()
    {
        // 1. Create a fake invalid file (e.g., pdf or text file)
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        // 2. Expect InvalidArgumentException
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Format foto harus berupa JPEG, JPG, atau PNG.');

        // 3. Trigger upload
        $this->service->uploadVehiclePhoto($file);
    }

    /** @test */
    public function it_throws_exception_if_file_size_exceeds_five_megabytes()
    {
        // 1. Create a fake file larger than 5MB (e.g., 5.1 MB = 5222 KB)
        $file = UploadedFile::fake()->image('huge_photo.png', 2000, 2000)->size(5222);

        // 2. Expect InvalidArgumentException
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Ukuran foto maksimal adalah 5MB.');

        // 3. Trigger upload
        $this->service->uploadVehiclePhoto($file);
    }

    /** @test */
    public function it_can_delete_an_uploaded_file()
    {
        // 1. Upload a file first
        $file = UploadedFile::fake()->image('to_delete.png')->size(50);
        $publicUrl = $this->service->uploadVehiclePhoto($file);
        $relativePath = str_replace('/storage/', '', $publicUrl);

        Storage::disk('public')->assertExists($relativePath);

        // 2. Delete it
        $deleted = $this->service->delete($publicUrl);

        // 3. Assert deletion was successful
        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($relativePath);
    }
}
