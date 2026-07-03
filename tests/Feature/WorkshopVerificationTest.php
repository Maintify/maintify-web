<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_workshop_verification_with_correct_relations()
    {
        // 1. Create dependencies
        $owner = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $workshop = Workshop::create([
            'user_id' => $owner->id,
            'name' => 'Signature Garage',
            'phone' => '089988776655',
            'email' => 'signature@garage.com',
            'address' => 'Golden Street 11',
            'is_active' => true,
            'status' => Workshop::STATUS_PENDING,
        ]);

        // 2. Create WorkshopVerification
        $verification = WorkshopVerification::create([
            'workshop_id' => $workshop->id,
            'reviewed_by' => $superAdmin->id,
            'status' => WorkshopVerification::STATUS_APPROVED,
            'rejection_reason' => null,
            'reviewed_at' => now(),
        ]);

        // 3. Assertions
        $this->assertDatabaseHas('workshop_verifications', [
            'id' => $verification->id,
            'workshop_id' => $workshop->id,
            'reviewed_by' => $superAdmin->id,
            'status' => WorkshopVerification::STATUS_APPROVED,
            'rejection_reason' => null,
        ]);

        // Check relationships
        $this->assertEquals($workshop->id, $verification->workshop->id);
        $this->assertEquals($superAdmin->id, $verification->reviewer->id);

        // Check inverse relationships
        $this->assertEquals($verification->id, $workshop->verification->id);
        $this->assertTrue($superAdmin->workshopReviews->contains($verification));
    }
}
