<?php

namespace Tests\Feature\Auth;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationOtpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_registration_sends_otp_and_redirects_to_otp_verify(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'vehicle_owner',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('auth.otp.verify'));

        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        $this->assertTrue(session()->has('otp_user_id'));
        $this->assertEquals($user->id, session('otp_user_id'));

        Mail::assertSent(OtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_user_can_verify_registration_otp_and_activates_account(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'vehicle_owner',
        ]);

        $otp = '123456';
        Cache::put("otp:{$user->id}", $otp, now()->addMinutes(5));

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp-verify', [
                'otp' => $otp,
            ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertFalse(Cache::has("otp:{$user->id}"));
    }

    public function test_user_cannot_verify_registration_with_invalid_otp(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'vehicle_owner',
        ]);

        $otp = '123456';
        Cache::put("otp:{$user->id}", $otp, now()->addMinutes(5));

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp-verify', [
                'otp' => '999999',
            ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('otp');

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    public function test_user_can_resend_registration_otp(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'vehicle_owner',
        ]);

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post(route('auth.otp.resend'));

        $response->assertRedirect();
        $response->assertSessionHas('status');

        Mail::assertSent(OtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_workshop_registration_redirects_to_workshop_form_after_otp(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'role' => 'workshop',
        ]);

        $otp = '654321';
        Cache::put("otp:{$user->id}", $otp, now()->addMinutes(5));

        $response = $this->withSession(['otp_user_id' => $user->id])
            ->post('/otp-verify', [
                'otp' => $otp,
            ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('register.workshop'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }
}
