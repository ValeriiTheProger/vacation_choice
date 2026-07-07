<?php

namespace Database\Factories;

use App\Models\VacationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VacationType>
 */
class VacationTypeFactory extends Factory
{
    protected $model = VacationType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'code' => fake()->unique()->slug(2),
        ];
    }
}
