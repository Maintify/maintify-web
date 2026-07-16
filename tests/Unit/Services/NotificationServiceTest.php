<?php

namespace Tests\Unit\Services;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificationService;
    }

    public function test_send_creates_notification_when_user_exists_and_preferences_allow(): void
    {
        $user = User::factory()->create([
            'enable_service_reminders' => true,
        ]);

        $notification = $this->service->send($user->id, 'general', 'Test Title', 'Test Message');

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'user_id' => $user->id,
            'type' => 'general',
            'title' => 'Test Title',
            'message' => 'Test Message',
            'is_read' => false,
        ]);
    }

    public function test_send_returns_null_when_user_does_not_exist(): void
    {
        $notification = $this->service->send(9999, 'general', 'Test Title', 'Test Message');

        $this->assertNull($notification);
        $this->assertDatabaseEmpty('notifications');
    }

    public function test_send_returns_null_when_service_reminder_is_disabled_by_user(): void
    {
        $user = User::factory()->create([
            'enable_service_reminders' => false,
        ]);

        $notification = $this->service->send($user->id, 'service_reminder', 'Reminder Title', 'Reminder Message');

        $this->assertNull($notification);
        $this->assertDatabaseEmpty('notifications');
    }

    public function test_send_creates_notification_when_service_reminder_is_enabled_by_user(): void
    {
        $user = User::factory()->create([
            'enable_service_reminders' => true,
        ]);

        $notification = $this->service->send($user->id, 'service_reminder', 'Reminder Title', 'Reminder Message');

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'type' => 'service_reminder',
        ]);
    }

    public function test_mark_as_read_updates_is_read_status(): void
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'general',
            'title' => 'Test Title',
            'message' => 'Test Message',
            'is_read' => false,
        ]);

        $updatedNotification = $this->service->markAsRead($notification);

        $this->assertTrue($updatedNotification->is_read);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_mark_all_as_read_updates_all_unread_notifications_for_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        // 3 unread for main user
        $n1 = Notification::create(['user_id' => $user->id, 'type' => 'general', 'title' => 'T1', 'message' => 'M1', 'is_read' => false]);
        $n2 = Notification::create(['user_id' => $user->id, 'type' => 'general', 'title' => 'T2', 'message' => 'M2', 'is_read' => false]);
        $n3 = Notification::create(['user_id' => $user->id, 'type' => 'general', 'title' => 'T3', 'message' => 'M3', 'is_read' => true]); // already read

        // 1 unread for other user
        $n4 = Notification::create(['user_id' => $otherUser->id, 'type' => 'general', 'title' => 'T4', 'message' => 'M4', 'is_read' => false]);

        $updatedCount = $this->service->markAllAsRead($user->id);

        $this->assertEquals(2, $updatedCount);

        $this->assertTrue($n1->fresh()->is_read);
        $this->assertTrue($n2->fresh()->is_read);
        $this->assertTrue($n3->fresh()->is_read);

        // Other user's notification should remain unread
        $this->assertFalse($n4->fresh()->is_read);
    }

    public function test_get_unread_count_returns_correct_count(): void
    {
        $user = User::factory()->create();

        Notification::create(['user_id' => $user->id, 'type' => 'general', 'title' => 'T1', 'message' => 'M1', 'is_read' => false]);
        Notification::create(['user_id' => $user->id, 'type' => 'general', 'title' => 'T2', 'message' => 'M2', 'is_read' => false]);
        Notification::create(['user_id' => $user->id, 'type' => 'general', 'title' => 'T3', 'message' => 'M3', 'is_read' => true]);

        $this->assertEquals(2, $this->service->getUnreadCount($user->id));
    }

    public function test_get_notifications_returns_paginated_notifications_ordered_by_newest(): void
    {
        $user = User::factory()->create();

        $n1 = new Notification;
        $n1->user_id = $user->id;
        $n1->type = 'general';
        $n1->title = 'T1';
        $n1->message = 'M1';
        $n1->is_read = false;
        $n1->created_at = now()->subMinutes(10);
        $n1->save();

        $n2 = new Notification;
        $n2->user_id = $user->id;
        $n2->type = 'general';
        $n2->title = 'T2';
        $n2->message = 'M2';
        $n2->is_read = false;
        $n2->created_at = now();
        $n2->save();

        $this->assertEquals(2, Notification::where('user_id', $user->id)->count());

        $paginator = $this->service->getNotifications($user->id, 15);

        $this->assertEquals(2, $paginator->total());
        $this->assertEquals($n2->id, $paginator->items()[0]->id); // newest first
        $this->assertEquals($n1->id, $paginator->items()[1]->id);
    }
}
