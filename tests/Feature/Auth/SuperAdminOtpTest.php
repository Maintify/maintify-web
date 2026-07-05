<?php

namespace Tests\Feature\Auth;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SuperAdminOtpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Super Admin dengan password benar diredirect ke OTP & dikirimi email.
     */
    public function test_super_admin_correct_password_redirects_to_otp_and_sends_email(): void
    {
        Mail::fake();

        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->post('/login', [
            'email' => $superAdmin->email,
            'password' => 'password', // Default password from user factory
        ]);

        $response->assertRedirect(route('auth.otp.verify'));
        $this->assertGuest();

        $this->assertTrue(session()->has('otp_user_id'));
        $this->assertEquals($superAdmin->id, session('otp_user_id'));

        $cacheKey = "otp:{$superAdmin->id}";
        $this->assertTrue(Cache::has($cacheKey));

        Mail::assertSent(function ($mail) use ($superAdmin) {
            return $mail->hasTo($superAdmin->email);
        });

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'login_attempt_otp_sent',
        ]);
    }

    /**
     * Test: Super Admin dengan password salah dicatat di audit log.
     */
    public function test_super_admin_incorrect_password_logs_failure(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->post('/login', [
            'email' => $superAdmin->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'login_failed_invalid_password',
        ]);
    }

    /**
     * Test: Super Admin bisa login dengan OTP yang valid.
     */
    public function test_super_admin_can_verify_valid_otp_and_log_in(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $otp = '123456';
        Cache::put("otp:{$superAdmin->id}", $otp, now()->addMinutes(5));

        $response = $this->withSession(['otp_user_id' => $superAdmin->id])
            ->post('/otp-verify', [
                'otp' => $otp,
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($superAdmin);

        $this->assertFalse(session()->has('otp_user_id'));
        $this->assertFalse(Cache::has("otp:{$superAdmin->id}"));

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'login_success',
        ]);
    }

    /**
     * Test: Super Admin ditolak jika memasukkan OTP salah.
     */
    public function test_super_admin_cannot_verify_invalid_otp(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $otp = '123456';
        Cache::put("otp:{$superAdmin->id}", $otp, now()->addMinutes(5));

        $response = $this->withSession(['otp_user_id' => $superAdmin->id])
            ->post('/otp-verify', [
                'otp' => '654321', // Wrong OTP
            ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'login_failed_invalid_otp',
        ]);
    }

    /**
     * Test: Super Admin ditolak jika OTP sudah kedaluwarsa (tidak ada di cache).
     */
    public function test_super_admin_cannot_verify_expired_otp(): void
    {
        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // Simulating expired OTP by not setting anything in Cache

        $response = $this->withSession(['otp_user_id' => $superAdmin->id])
            ->post('/otp-verify', [
                'otp' => '123456',
            ]);

        $response->assertSessionHasErrors('otp');
        $this->assertGuest();

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'login_failed_expired_otp',
        ]);
    }

    /**
     * Test: Super Admin bisa mengirim ulang OTP.
     */
    public function test_super_admin_can_resend_otp(): void
    {
        Mail::fake();

        $superAdmin = User::factory()->create([
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        $response = $this->withSession(['otp_user_id' => $superAdmin->id])
            ->post(route('auth.otp.resend'));

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Kode OTP baru telah dikirim ke email Anda.');

        $cacheKey = "otp:{$superAdmin->id}";
        $this->assertTrue(Cache::has($cacheKey));

        Mail::assertSent(function ($mail) use ($superAdmin) {
            return $mail->hasTo($superAdmin->email);
        });

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $superAdmin->id,
            'action' => 'login_attempt_otp_resent',
        ]);
    }

    /**
     * Test: User non-Super Admin (misal: vehicle_owner) login langsung bypass OTP.
     */
    public function test_non_super_admin_login_bypasses_otp(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_VEHICLE_OWNER,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertFalse(session()->has('otp_user_id'));
    }
}
