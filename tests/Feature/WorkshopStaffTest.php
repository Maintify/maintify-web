<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopStaffTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_workshop_staff_with_correct_relations()
    {
        // 1. Create dependencies
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $staffUser = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Speedy Repair',
            'phone' => '081199887766',
            'email' => 'speedy@repair.com',
            'address' => 'Speedway 99',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        // 2. Create WorkshopStaff
        $workshopStaff = WorkshopStaff::create([
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
            'joined_at' => now(),
        ]);

        // 3. Assertions
        $this->assertDatabaseHas('workshop_staff', [
            'id' => $workshopStaff->id,
            'workshop_id' => $workshop->id,
            'user_id' => $staffUser->id,
            'position' => WorkshopStaff::POSITION_MECHANIC,
            'is_active' => true,
        ]);

        // Check relationships
        $this->assertEquals($workshop->id, $workshopStaff->workshop->id);
        $this->assertEquals($staffUser->id, $workshopStaff->user->id);

        // Check inverse relationships
        $this->assertTrue($workshop->staff->contains($workshopStaff));
        $this->assertEquals($workshopStaff->id, $staffUser->workshopStaff->id);
    }
}
