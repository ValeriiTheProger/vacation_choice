<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Destination>
 */
class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'country_id' => Country::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 100000),
            'short_description' => fake()->sentence(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'flight_minutes_from_vienna' => fake()->numberBetween(60, 360),
            'min_days' => 3,
            'max_days' => 10,
            'image_path' => null,
            'is_active' => true,
        ];
    }
}
