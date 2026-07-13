<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    /** @test */
    public function user_can_view_notifications_list()
    {
        $user = User::factory()->create();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'test_alert',
            'title' => 'Test Notification 1',
            'message' => 'This is a test notification 1 body message.',
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'test_alert',
            'title' => 'Test Notification 2',
            'message' => 'This is a test notification 2 body message.',
            'is_read' => true,
        ]);

        $response = $this->actingAs($user)->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Notification 1');
        $response->assertSee('Test Notification 2');
        $response->assertSee('This is a test notification 1 body message.');
    }

    /** @test */
    public function bell_icon_shows_correct_unread_count()
    {
        $user = User::factory()->create();

        // 3 unread notifications
        Notification::create([
            'user_id' => $user->id,
            'type' => 'alert',
            'title' => 'Unread 1',
            'message' => 'Msg',
            'is_read' => false,
        ]);
        Notification::create([
            'user_id' => $user->id,
            'type' => 'alert',
            'title' => 'Unread 2',
            'message' => 'Msg',
            'is_read' => false,
        ]);
        Notification::create([
            'user_id' => $user->id,
            'type' => 'alert',
            'title' => 'Unread 3',
            'message' => 'Msg',
            'is_read' => false,
        ]);

        // 1 read notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'alert',
            'title' => 'Read 1',
            'message' => 'Msg',
            'is_read' => true,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);

        // Assert unread count badge is rendered (3)
        $response->assertSee('3');
        $response->assertSee('Unread 1');
        $response->assertSee('Unread 3');
    }

    /** @test */
    public function user_can_mark_notification_as_read()
    {
        $user = User::factory()->create();

        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'test_alert',
            'title' => 'Unread Notification',
            'message' => 'Message body',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->post(route('notifications.read', $notification->id));

        $response->assertRedirect();
        $this->assertTrue($notification->refresh()->is_read);
    }

    /** @test */
    public function user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();

        $notif1 = Notification::create([
            'user_id' => $user->id,
            'type' => 'test',
            'title' => 'Notif 1',
            'message' => 'Msg',
            'is_read' => false,
        ]);

        $notif2 = Notification::create([
            'user_id' => $user->id,
            'type' => 'test',
            'title' => 'Notif 2',
            'message' => 'Msg',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->post(route('notifications.read-all'));

        $response->assertRedirect();
        $this->assertTrue($notif1->refresh()->is_read);
        $this->assertTrue($notif2->refresh()->is_read);
    }

    /** @test */
    public function user_cannot_mark_other_users_notifications_as_read()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $notificationOfB = Notification::create([
            'user_id' => $userB->id,
            'type' => 'test',
            'title' => 'Notif B',
            'message' => 'Msg',
            'is_read' => false,
        ]);

        // User A requests to mark User B's notification as read
        $response = $this->actingAs($userA)->post(route('notifications.read', $notificationOfB->id));

        $response->assertStatus(403);
        $this->assertFalse($notificationOfB->refresh()->is_read);
    }
}
