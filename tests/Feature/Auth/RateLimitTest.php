<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiter cache before each test
        RateLimiter::clear($this->getThrottleKey('test@example.com', '127.0.0.1'));
    }

    /**
     * Test: Setelah 5 kali gagal login, percobaan ke-6 diblokir dengan status 429.
     */
    public function test_login_attempts_are_rate_limited_after_five_failures(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $ip = '127.0.0.1';

        // Lakukan 5 kali percobaan gagal
        for ($i = 0; $i < 5; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
                ->post('/login', [
                    'email' => $user->email,
                    'password' => 'wrong-password',
                ]);

            $response->assertSessionHasErrors('email');
            $this->assertGuest();
        }

        // Percobaan ke-6 harus diblokir dengan HTTP 429 (Too Many Requests)
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);

        $response->assertStatus(429);
        $this->assertStringContainsString('Terlalu banyak percobaan masuk', $response->content());
    }

    /**
     * Test: Rate limit berlaku spesifik per kombinasi Email + IP.
     */
    public function test_rate_limit_applies_per_email_and_ip_combination(): void
    {
        $userA = User::factory()->create([
            'email' => 'usera@maintify.app',
            'password' => bcrypt('password'),
        ]);

        $userB = User::factory()->create([
            'email' => 'userb@maintify.app',
            'password' => bcrypt('password'),
        ]);

        $ip1 = '1.1.1.1';
        $ip2 = '2.2.2.2';

        // Clear rate limiters
        RateLimiter::clear($this->getThrottleKey($userA->email, $ip1));
        RateLimiter::clear($this->getThrottleKey($userA->email, $ip2));
        RateLimiter::clear($this->getThrottleKey($userB->email, $ip1));

        // Blokir kombinasi: userA + IP1 (5 kali gagal)
        for ($i = 0; $i < 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ip1])
                ->post('/login', [
                    'email' => $userA->email,
                    'password' => 'wrong-password',
                ]);
        }

        // Percobaan ke-6 userA dari IP1 harus diblokir (429)
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip1])
            ->post('/login', [
                'email' => $userA->email,
                'password' => 'wrong-password',
            ]);
        $response->assertStatus(429);

        // Percobaan userA dari IP2 (IP berbeda) harusnya lolos dari blokir 429 (hanya error password 302/422)
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip2])
            ->post('/login', [
                'email' => $userA->email,
                'password' => 'wrong-password',
            ]);
        $response->assertStatus(302);

        // Percobaan userB dari IP1 (email berbeda dari IP sama) juga harus lolos dari blokir 429 (hanya error password 302)
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ip1])
            ->post('/login', [
                'email' => $userB->email,
                'password' => 'wrong-password',
            ]);
        $response->assertStatus(302);
    }

    /**
     * Helper to reconstruct the throttle key.
     */
    protected function getThrottleKey(string $email, string $ip): string
    {
        return mb_strtolower($email).'|'.$ip;
    }
}
