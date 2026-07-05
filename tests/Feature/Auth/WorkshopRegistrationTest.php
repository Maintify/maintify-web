<?php
 
namespace Tests\Feature\Auth;
 
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
 
class WorkshopRegistrationTest extends TestCase
{
    use RefreshDatabase;
 
    /**
     * Test form pendaftaran dapat dirender.
     */
    public function test_workshop_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register.workshop'));
 
        $response->assertStatus(200);
        $response->assertSee('Daftar Bengkel Mitra');
    }
 
    /**
     * Test pendaftaran berhasil dengan data yang valid.
     */
    public function test_new_workshop_can_register_successfully(): void
    {
        Storage::fake('public');
 
        $file = UploadedFile::fake()->create('nib_document.pdf', 2048, 'application/pdf');
 
        $response = $this->post('/register/workshop', [
            // Pemilik
            'owner_name' => 'John Owner',
            'email' => 'owner@example.com',
            'owner_ktp_number' => '1234567890123456',
            // Bengkel
            'workshop_name' => 'John Auto Service',
            'phone' => '08123456789',
            'address' => 'Jl. Otomotif No. 100',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'operational_hours' => 'Senin - Jumat: 09:00 - 18:00',
            // Dokumen & Password
            'legal_document' => $file,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
 
        // 1. Harus redirect ke halaman pending
        $response->assertRedirect(route('workshop.pending'));
 
        // 2. User harus terautentikasi
        $this->assertAuthenticated();
 
        // 3. User & Workshop harus ada di database
        $this->assertDatabaseHas('users', [
            'name' => 'John Owner',
            'email' => 'owner@example.com',
            'role' => User::ROLE_WORKSHOP,
        ]);
 
        $user = User::where('email', 'owner@example.com')->first();
        $this->assertNotNull($user);
 
        $this->assertDatabaseHas('workshops', [
            'user_id' => $user->id,
            'name' => 'John Auto Service',
            'phone' => '08123456789',
            'email' => 'owner@example.com',
            'owner_name' => 'John Owner',
            'owner_ktp_number' => '1234567890123456',
            'operational_hours' => 'Senin - Jumat: 09:00 - 18:00',
            'status' => 'pending',
            'is_active' => false,
        ]);
 
        // 4. File harus terunggah
        $workshop = Workshop::where('user_id', $user->id)->first();
        $this->assertNotNull($workshop->legal_document_url);
        Storage::disk('public')->assertExists($workshop->legal_document_url);
    }
 
    /**
     * Test form validation rules for required inputs.
     */
    public function test_workshop_registration_requires_mandatory_fields(): void
    {
        $response = $this->post('/register/workshop', []);
 
        $response->assertSessionHasErrors([
            'owner_name', 'email', 'owner_ktp_number',
            'workshop_name', 'phone', 'address', 'city', 'province', 'operational_hours',
            'legal_document', 'password'
        ]);
 
        $this->assertGuest();
    }
 
    /**
     * Test KTP harus berupa 16 digit angka.
     */
    public function test_workshop_registration_validates_ktp_digits(): void
    {
        $response = $this->post('/register/workshop', [
            'owner_ktp_number' => '12345', // Kurang dari 16 digit
        ]);
 
        $response->assertSessionHasErrors('owner_ktp_number');
        
        $response2 = $this->post('/register/workshop', [
            'owner_ktp_number' => '123456789012345a', // Ada huruf
        ]);
 
        $response2->assertSessionHasErrors('owner_ktp_number');
    }
 
    /**
     * Test file format and size limits.
     */
    public function test_workshop_registration_validates_legal_document_format_and_size(): void
    {
        Storage::fake('public');
 
        // File tidak valid (format text/plain)
        $invalidFile = UploadedFile::fake()->create('document.txt', 100, 'text/plain');
        $response = $this->post('/register/workshop', [
            'legal_document' => $invalidFile,
        ]);
        $response->assertSessionHasErrors('legal_document');
 
        // File terlalu besar (15MB)
        $largeFile = UploadedFile::fake()->create('nib_doc.pdf', 15360, 'application/pdf');
        $response2 = $this->post('/register/workshop', [
            'legal_document' => $largeFile,
        ]);
        $response2->assertSessionHasErrors('legal_document');
    }
}
