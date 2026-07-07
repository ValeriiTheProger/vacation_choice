<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\MonthlyWeather;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MonthlyWeather>
 */
class MonthlyWeatherFactory extends Factory
{
    protected $model = MonthlyWeather::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'destination_id' => Destination::factory(),
            'month' => fake()->numberBetween(1, 12),
            'avg_temp' => fake()->randomFloat(1, 5, 35),
            'min_temp' => fake()->randomFloat(1, 0, 20),
            'max_temp' => fake()->randomFloat(1, 20, 40),
            'rainy_days' => fake()->numberBetween(0, 20),
        ];
    }
}
