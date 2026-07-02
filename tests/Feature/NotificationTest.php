<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_notification_with_correct_relations()
    {
        // 1. Create user
        $user = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        // 2. Create Notification
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'service_reminder',
            'title' => 'Pengingat Service',
            'message' => 'Kendaraan Anda sudah waktunya untuk service berkala.',
            'is_read' => false,
        ]);

        // 3. Assertions — database
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'user_id' => $user->id,
            'type' => 'service_reminder',
            'is_read' => false,
        ]);

        // 4. Assertions — model relationship
        $this->assertEquals($user->id, $notification->user->id);

        // 5. Assertions — inverse relationship
        $this->assertTrue($user->notifications->contains($notification));
    }

    /** @test */
    public function is_read_defaults_to_false()
    {
        $user = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'transfer_request',
            'title' => 'Permintaan Transfer',
            'message' => 'Ada permintaan transfer kepemilikan kendaraan.',
        ]);

        $this->assertFalse($notification->fresh()->is_read);
    }

    /** @test */
    public function it_can_scope_unread_and_read_notifications()
    {
        $user = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);

        // Create unread notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Unread',
            'message' => 'Belum dibaca.',
            'is_read' => false,
        ]);

        // Create read notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Read',
            'message' => 'Sudah dibaca.',
            'is_read' => true,
        ]);

        $this->assertCount(1, Notification::unread()->get());
        $this->assertCount(1, Notification::read()->get());
    }
}
