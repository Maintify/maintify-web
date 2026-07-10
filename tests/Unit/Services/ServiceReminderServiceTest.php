<?php

namespace Tests\Unit\Services;

use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\ServiceReminderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ServiceReminderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Atur default setting agar konsisten
        Setting::set('service_reminder_interval', 180);
        Setting::set('service_reminder_mileage', 5000);
    }

    /** @test */
    public function it_sends_time_based_reminder_when_next_service_date_is_past_or_today()
    {
        $owner = User::factory()->create();
        
        // 1. Overdue vehicle
        $vehicleOverdue = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 1111 AAA',
            'brand' => 'Honda',
            'model' => 'BeAT',
            'year' => 2021,
            'is_active' => true,
            'next_service_date' => Carbon::yesterday()->toDateString(),
        ]);

        // 2. Future vehicle (not due)
        $vehicleFuture = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 2222 AAA',
            'brand' => 'Honda',
            'model' => 'Vario',
            'year' => 2022,
            'is_active' => true,
            'next_service_date' => Carbon::tomorrow()->toDateString(),
        ]);

        $service = new ServiceReminderService();
        $result = $service->sendServiceReminders();

        $this->assertEquals(1, $result['time_reminders_sent']);

        // Assert notification created for overdue vehicle
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'service_reminder',
            'title' => 'Jadwal Servis Berkala Lewat Batas',
        ]);

        $notification = Notification::where('user_id', $owner->id)->first();
        $this->assertStringContainsString('B 1111 AAA', $notification->message);
        $this->assertStringNotContainsString('B 2222 AAA', $notification->message);
    }

    /** @test */
    public function it_sends_mileage_based_reminder_when_current_odometer_nears_next_service_odometer()
    {
        $owner = User::factory()->create();

        // service_reminder_mileage is 5000, so warning threshold is max(1000, 0.2*5000) = 1000 km.
        // 1. Vehicle nearing threshold: gap is 900 km (<= 1000 km threshold)
        $vehicleNearing = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 3333 AAA',
            'brand' => 'Suzuki',
            'model' => 'Nex',
            'year' => 2021,
            'is_active' => true,
            'current_odometer' => 9100,
            'next_service_odometer' => 10000,
        ]);

        // 2. Vehicle far from threshold: gap is 1500 km (> 1000 km threshold)
        $vehicleFar = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 4444 AAA',
            'brand' => 'Suzuki',
            'model' => 'Address',
            'year' => 2022,
            'is_active' => true,
            'current_odometer' => 8500,
            'next_service_odometer' => 10000,
        ]);

        $service = new ServiceReminderService();
        $result = $service->sendServiceReminders();

        $this->assertEquals(1, $result['mileage_reminders_sent']);

        // Assert notification created for nearing vehicle
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'service_reminder',
            'title' => 'Mendekati Batas Odometer Servis',
        ]);

        $notification = Notification::where('user_id', $owner->id)
            ->where('title', 'Mendekati Batas Odometer Servis')
            ->first();
        $this->assertStringContainsString('B 3333 AAA', $notification->message);
        $this->assertStringNotContainsString('B 4444 AAA', $notification->message);
    }

    /** @test */
    public function it_prevents_duplicate_reminders_for_same_service_window()
    {
        $owner = User::factory()->create();
        
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 5555 AAA',
            'brand' => 'Yamaha',
            'model' => 'NMax',
            'year' => 2020,
            'is_active' => true,
            'next_service_date' => Carbon::yesterday()->toDateString(),
            'current_odometer' => 9500,
            'next_service_odometer' => 10000,
        ]);

        $service = new ServiceReminderService();

        // First run -> should send both time and mileage reminders
        $result1 = $service->sendServiceReminders();
        $this->assertEquals(1, $result1['time_reminders_sent']);
        $this->assertEquals(1, $result1['mileage_reminders_sent']);

        // Second run -> should send zero reminders (duplicate prevention)
        $result2 = $service->sendServiceReminders();
        $this->assertEquals(0, $result2['time_reminders_sent']);
        $this->assertEquals(0, $result2['mileage_reminders_sent']);

        $totalNotificationsCount = Notification::where('user_id', $owner->id)->count();
        $this->assertEquals(2, $totalNotificationsCount);
    }

    /** @test */
    public function it_can_execute_via_console_command()
    {
        $owner = User::factory()->create();
        
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 6666 AAA',
            'brand' => 'Honda',
            'model' => 'PCX',
            'year' => 2021,
            'is_active' => true,
            'next_service_date' => Carbon::yesterday()->toDateString(),
        ]);

        // Run artisan command
        $exitCode = Artisan::call('maintify:send-reminders');

        $this->assertEquals(0, $exitCode);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'type' => 'service_reminder',
            'title' => 'Jadwal Servis Berkala Lewat Batas',
        ]);
    }
}
