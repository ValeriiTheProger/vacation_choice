<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
            'name_sk' => fake()->unique()->country(),
            'iso_code' => strtoupper(fake()->unique()->lexify('???')),
            'capital' => fake()->city(),
            'currency_code' => strtoupper(fake()->lexify('???')),
        ];
    }
}
