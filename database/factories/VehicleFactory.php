<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plate_number' => strtoupper(fake()->bothify('? #### ???')),
            'brand' => fake()->randomElement(['Honda', 'Yamaha', 'Suzuki', 'Kawasaki', 'Toyota']),
            'model' => fake()->randomElement(['Vario 160', 'NMAX', 'Beat', 'Scoopy', 'Avanza']),
            'type' => fake()->optional()->randomElement(['CBS', 'ABS', 'Standard']),
            'year' => fake()->numberBetween(2015, (int) date('Y')),
            'color' => fake()->randomElement(['Hitam', 'Putih', 'Merah', 'Biru', 'Silver']),
            'fuel_type' => fake()->randomElement(['gasoline', 'diesel', 'electric', 'hybrid']),
            'engine_number' => strtoupper(fake()->bothify('??###-######')),
            'chassis_number' => strtoupper(Str::random(17)),
            'current_odometer' => fake()->numberBetween(0, 100000),
            'health_status' => 'good',
            'health_score' => 100,
            'is_active' => true,
        ];
    }

    /**
     * State: vehicle owned by a specific user.
     */
    public function ownedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * State: vehicle with critical health.
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'health_status' => 'critical',
            'health_score' => fake()->numberBetween(0, 30),
        ]);
    }

    /**
     * State: vehicle with warning health.
     */
    public function warning(): static
    {
        return $this->state(fn (array $attributes) => [
            'health_status' => 'warning',
            'health_score' => fake()->numberBetween(31, 60),
        ]);
    }
}
