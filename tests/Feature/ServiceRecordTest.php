<?php

namespace Tests\Feature;

use App\Models\ServicePart;
use App\Models\ServiceRecord;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRecordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_service_record_and_parts_with_correct_relations()
    {
        // 1. Create dependencies
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $mechanic = User::factory()->create(['role' => User::ROLE_WORKSHOP]);

        $workshop = Workshop::create([
            'user_id' => $mechanic->id,
            'name' => 'Auto Service Test',
            'phone' => '08122334455',
            'email' => 'auto@test.com',
            'address' => 'Test Road 456',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 5678 XYZ',
            'brand' => 'Yamaha',
            'model' => 'NMAX',
            'year' => 2023,
            'color' => 'Grey',
            'current_odometer' => 5000,
            'health_status' => 'good',
            'is_active' => true,
        ]);

        // 2. Create Service Record
        $serviceRecord = ServiceRecord::create([
            'vehicle_id' => $vehicle->id,
            'workshop_id' => $workshop->id,
            'performed_by' => $mechanic->id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 5200,
            'mechanic_notes' => 'Ganti oli mesin Shell Advance',
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 150000.00,
            'service_date' => now(),
        ]);

        // 3. Create Service Parts
        $part = ServicePart::create([
            'service_record_id' => $serviceRecord->id,
            'part_name' => 'Oli Shell Advance Ultra 10W-40',
            'quantity' => 1,
            'unit_price' => 135000.00,
            'part_category' => 'oil',
        ]);

        $gasket = ServicePart::create([
            'service_record_id' => $serviceRecord->id,
            'part_name' => 'Gasket Drain Plug',
            'quantity' => 1,
            'unit_price' => 15000.00,
            'part_category' => 'other',
        ]);

        // 4. Assertions
        $this->assertDatabaseHas('service_records', [
            'id' => $serviceRecord->id,
            'service_type' => ServiceRecord::TYPE_OIL_CHANGE,
            'odometer_at_service' => 5200,
            'mechanic_notes' => 'Ganti oli mesin Shell Advance',
            'status' => ServiceRecord::STATUS_COMPLETED,
            'total_cost' => 150000.00,
        ]);

        $this->assertDatabaseHas('service_parts', [
            'service_record_id' => $serviceRecord->id,
            'part_name' => 'Oli Shell Advance Ultra 10W-40',
            'quantity' => 1,
            'unit_price' => 135000.00,
        ]);

        // Check relationships
        $this->assertEquals($vehicle->id, $serviceRecord->vehicle->id);
        $this->assertEquals($workshop->id, $serviceRecord->workshop->id);
        $this->assertEquals($mechanic->id, $serviceRecord->performedBy->id);

        $this->assertCount(2, $serviceRecord->parts);
        $this->assertEquals($serviceRecord->id, $part->serviceRecord->id);

        // Check helpers
        $this->assertEquals('Ganti Oli', $serviceRecord->serviceTypeLabelReadable);
        $this->assertEquals('Rp 150.000', $serviceRecord->formatted_cost);

        $this->assertEquals(135000.00, $part->subtotal);
        $this->assertEquals('Rp 135.000', $part->formatted_subtotal);

        // Check reverse relations in vehicle and workshop
        $this->assertTrue($vehicle->serviceRecords->contains($serviceRecord));
        $this->assertTrue($workshop->serviceRecords->contains($serviceRecord));
    }
}
