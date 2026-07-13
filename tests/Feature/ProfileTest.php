<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Services\ServiceReminderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_profile_photo_and_phone_number_can_be_updated(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $photo = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'John Doe Updated',
                'email' => $user->email,
                'phone_number' => '08123456789',
                'photo' => $photo,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('John Doe Updated', $user->name);
        $this->assertSame('08123456789', $user->phone_number);
        $this->assertNotNull($user->photo_url);

        // Verify file stored in public disk
        $relativePath = str_replace('/storage/', '', $user->photo_url);
        Storage::disk('public')->assertExists($relativePath);
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertTrue(Hash::check('new-password123', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }

    public function test_notification_preferences_page_is_displayed_and_can_be_updated(): void
    {
        $user = User::factory()->create([
            'enable_service_reminders' => true,
            'enable_email_notifications' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
        $response->assertSee('Pengaturan Notifikasi');
        $response->assertSee('enable_service_reminders');

        // Update preferences (disable both)
        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile/notifications', [
                // If checkbox is unchecked, it will be omitted from the request
            ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertFalse($user->enable_service_reminders);
        $this->assertFalse($user->enable_email_notifications);
    }

    public function test_service_reminders_are_not_sent_if_preference_is_disabled(): void
    {
        $owner = User::factory()->create([
            'enable_service_reminders' => false,
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 7777 AAA',
            'brand' => 'Honda',
            'model' => 'Spacy',
            'year' => 2021,
            'is_active' => true,
            'next_service_date' => Carbon::yesterday()->toDateString(),
            'current_odometer' => 9500,
            'next_service_odometer' => 10000,
        ]);

        $service = new ServiceReminderService;
        $result = $service->sendServiceReminders();

        $this->assertEquals(0, $result['time_reminders_sent']);
        $this->assertEquals(0, $result['mileage_reminders_sent']);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $owner->id,
            'type' => 'service_reminder',
        ]);
    }
}
