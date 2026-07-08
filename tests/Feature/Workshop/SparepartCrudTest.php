<?php

namespace Tests\Feature\Workshop;

use App\Models\Sparepart;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SparepartCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function createApprovedWorkshop(): array
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKSHOP]);
        $workshop = Workshop::create([
            'user_id' => $user->id,
            'name' => 'Bengkel Utama',
            'phone' => '081200000001',
            'email' => 'utama@bengkel.com',
            'address' => 'Jl. Utama No. 1',
            'is_active' => true,
            'status' => Workshop::STATUS_APPROVED,
        ]);

        return [$user, $workshop];
    }

    /** @test */
    public function approved_workshop_user_can_view_spareparts_catalog()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        $part1 = Sparepart::create([
            'workshop_id' => $workshop->id,
            'name' => 'Oli Shell HX7',
            'category' => 'Oli',
            'price' => 95000,
        ]);

        $part2 = Sparepart::create([
            'workshop_id' => $workshop->id,
            'name' => 'Kampas Rem Vario',
            'category' => 'Rem',
            'price' => 60000,
        ]);

        $response = $this->actingAs($user)
            ->get(route('workshop.spareparts.index'));

        $response->assertStatus(200);
        $response->assertViewIs('workshop.spareparts.index');
        $response->assertSee('Oli Shell HX7');
        $response->assertSee('Kampas Rem Vario');
    }

    /** @test */
    public function approved_workshop_user_can_search_spareparts_catalog()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        Sparepart::create([
            'workshop_id' => $workshop->id,
            'name' => 'Oli Shell HX7',
            'category' => 'Oli',
            'price' => 95000,
        ]);

        Sparepart::create([
            'workshop_id' => $workshop->id,
            'name' => 'Kampas Rem Vario',
            'category' => 'Rem',
            'price' => 60000,
        ]);

        $response = $this->actingAs($user)
            ->get(route('workshop.spareparts.index', ['search' => 'HX7']));

        $response->assertSee('Oli Shell HX7');
        $response->assertDontSee('Kampas Rem Vario');
    }

    /** @test */
    public function approved_workshop_user_can_create_sparepart()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        $payload = [
            'name' => 'Busi Iridium',
            'category' => 'Kelistrikan',
            'price' => 120000,
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)
            ->post(route('workshop.spareparts.store'), $payload);

        $response->assertRedirect(route('workshop.spareparts.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('spareparts', [
            'workshop_id' => $workshop->id,
            'name' => 'Busi Iridium',
            'category' => 'Kelistrikan',
            'price' => 120000.00,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function sparepart_creation_requires_name_and_valid_price()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        $payload = [
            'name' => '', // required
            'price' => -500, // min:0
        ];

        $response = $this->actingAs($user)
            ->post(route('workshop.spareparts.store'), $payload);

        $response->assertSessionHasErrors(['name', 'price']);
    }

    /** @test */
    public function approved_workshop_user_can_edit_and_update_sparepart()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        $part = Sparepart::create([
            'workshop_id' => $workshop->id,
            'name' => 'Oli Murah',
            'category' => 'Oli',
            'price' => 20000,
        ]);

        $payload = [
            'name' => 'Oli Mahal',
            'category' => 'Oli Super',
            'price' => 150000,
            'is_active' => '0',
        ];

        $response = $this->actingAs($user)
            ->put(route('workshop.spareparts.update', $part), $payload);

        $response->assertRedirect(route('workshop.spareparts.index'));
        $response->assertSessionHas('success');

        $part->refresh();
        $this->assertEquals('Oli Mahal', $part->name);
        $this->assertEquals('Oli Super', $part->category);
        $this->assertEquals(150000.00, $part->price);
        $this->assertFalse($part->is_active);
    }

    /** @test */
    public function approved_workshop_user_can_delete_sparepart()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        $part = Sparepart::create([
            'workshop_id' => $workshop->id,
            'name' => 'Barang Rusak',
            'category' => 'Lainnya',
            'price' => 1000,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('workshop.spareparts.destroy', $part));

        $response->assertRedirect(route('workshop.spareparts.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('spareparts', [
            'id' => $part->id,
        ]);
    }

    /** @test */
    public function workshop_cannot_manage_another_workshops_spareparts()
    {
        [$user1, $workshop1] = $this->createApprovedWorkshop();
        [$user2, $workshop2] = $this->createApprovedWorkshop();

        $part1 = Sparepart::create([
            'workshop_id' => $workshop1->id,
            'name' => 'Oli Shell HX7',
            'category' => 'Oli',
            'price' => 95000,
        ]);

        // Workshop 2 tries to edit Workshop 1's sparepart
        $response1 = $this->actingAs($user2)
            ->get(route('workshop.spareparts.edit', $part1));
        $response1->assertStatus(403);

        // Workshop 2 tries to update Workshop 1's sparepart
        $response2 = $this->actingAs($user2)
            ->put(route('workshop.spareparts.update', $part1), [
                'name' => 'Hack Name',
                'price' => 100,
            ]);
        $response2->assertStatus(403);

        // Workshop 2 tries to delete Workshop 1's sparepart
        $response3 = $this->actingAs($user2)
            ->delete(route('workshop.spareparts.destroy', $part1));
        $response3->assertStatus(403);
    }

    /** @test */
    public function spareparts_are_loaded_into_service_record_form()
    {
        [$user, $workshop] = $this->createApprovedWorkshop();

        $part = Sparepart::create([
            'workshop_id' => $workshop->id,
            'name' => 'Autofill Part',
            'category' => 'Tester',
            'price' => 50000,
            'is_active' => true,
        ]);

        // Create a vehicle and owner for the form context
        $owner = User::factory()->create(['role' => User::ROLE_VEHICLE_OWNER]);
        $vehicle = Vehicle::create([
            'user_id' => $owner->id,
            'plate_number' => 'B 777 OK',
            'brand' => 'Suzuki',
            'model' => 'Hayabusa',
            'year' => 2021,
            'fuel_type' => 'gasoline',
            'current_odometer' => 5000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('workshop.service-records.create', ['vehicle_id' => $vehicle->id]));

        $response->assertStatus(200);
        $response->assertSee('Autofill Part');
    }
}
