<?php

namespace Tests\Feature\Services;

use App\Models\Country;
use App\Models\Destination;
use App\Models\VacationType;
use App\Services\DestinationRecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestinationRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    private DestinationRecommendationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new DestinationRecommendationService();
    }

    private function makeDestination(array $attributes = []): Destination
    {
        return Destination::factory()->create(array_merge([
            'country_id' => Country::factory(),
            'min_days' => 3,
            'max_days' => 10,
        ], $attributes));
    }

    private function baseFilters(array $overrides = []): array
    {
        return array_merge([
            'month' => 6,
            'days' => 5,
            'types' => [],
            'temperature' => 'any',
            'distance' => 'any',
        ], $overrides);
    }

    public function test_it_only_recommends_active_destinations(): void
    {
        $this->makeDestination(['is_active' => true, 'name' => 'Active One']);
        $this->makeDestination(['is_active' => false, 'name' => 'Inactive One']);

        $results = $this->service->recommend($this->baseFilters());

        $this->assertCount(1, $results);
        $this->assertSame('Active One', $results->first()->name);
    }

    public function test_full_type_match_outranks_partial_and_no_match(): void
    {
        $beach = VacationType::factory()->create(['code' => 'beach']);
        $nature = VacationType::factory()->create(['code' => 'nature']);
        VacationType::factory()->create(['code' => 'history']);

        $fullMatch = $this->makeDestination(['name' => 'Full Match']);
        $fullMatch->vacationTypes()->attach([$beach->id, $nature->id]);

        $partialMatch = $this->makeDestination(['name' => 'Partial Match']);
        $partialMatch->vacationTypes()->attach([$beach->id]);

        $noMatch = $this->makeDestination(['name' => 'No Match']);

        $results = $this->service->recommend($this->baseFilters([
            'types' => ['beach', 'nature'],
        ]));

        $names = $results->pluck('name')->all();

        $this->assertSame(['Full Match', 'Partial Match', 'No Match'], $names);
        $this->assertGreaterThan($results[1]->score, $results[0]->score);
        $this->assertGreaterThan($results[2]->score, $results[1]->score);
    }

    public function test_empty_type_filter_gives_every_destination_the_same_base_type_score(): void
    {
        $destination = $this->makeDestination();
        $destination->monthlyWeather()->create([
            'month' => 6,
            'avg_temp' => 25,
            'min_temp' => 20,
            'max_temp' => 30,
            'rainy_days' => 1,
        ]);

        $results = $this->service->recommend($this->baseFilters(['types' => []]));

        // 20 (empty type filter) + 20 (temperature "any") + 15 (distance "any") + 10 (days in range)
        $this->assertSame(65, $results->first()->score);
    }

    public function test_temperature_score_for_exact_range_match(): void
    {
        $destination = $this->makeDestination();
        $destination->monthlyWeather()->create([
            'month' => 6,
            'avg_temp' => 25,
            'min_temp' => 18,
            'max_temp' => 30,
            'rainy_days' => 2,
        ]);

        $results = $this->service->recommend($this->baseFilters(['temperature' => 'warm']));

        // 20 (empty types) + 30 (25°C is within the "warm" 20-29 range) + 15 (distance any) + 10 (days in range)
        $this->assertSame(75, $results->first()->score);
    }

    public function test_temperature_score_decreases_as_the_temperature_moves_away_from_the_preferred_range(): void
    {
        $cases = [
            // avg_temp, expected temperature score for "warm" (20-29)
            [32, 20], // 3° over the range -> small penalty
            [33, 10], // 4° over the range -> bigger penalty
            [36, 0],  // 7° over the range -> no match
        ];

        foreach ($cases as [$avgTemp, $expectedTemperatureScore]) {
            $destination = $this->makeDestination();
            $destination->monthlyWeather()->create([
                'month' => 6,
                'avg_temp' => $avgTemp,
                'min_temp' => $avgTemp - 5,
                'max_temp' => $avgTemp + 5,
                'rainy_days' => 0,
            ]);

            $results = $this->service->recommend($this->baseFilters(['temperature' => 'warm']));

            $expectedScore = 20 + $expectedTemperatureScore + 15 + 10;
            $this->assertSame($expectedScore, $results->firstWhere('id', $destination->id)->score, "Failed for avg_temp={$avgTemp}");
        }
    }

    public function test_destination_without_weather_data_for_the_month_gets_no_temperature_score(): void
    {
        $destination = $this->makeDestination();
        $destination->monthlyWeather()->create([
            'month' => 1,
            'avg_temp' => 25,
            'min_temp' => 20,
            'max_temp' => 30,
            'rainy_days' => 1,
        ]);

        $results = $this->service->recommend($this->baseFilters(['month' => 6, 'temperature' => 'warm']));

        // No weather row for month 6, so temperature contributes 0: 20 + 0 + 15 + 10
        $this->assertSame(45, $results->first()->score);
    }

    public function test_distance_score_for_the_3h_preference(): void
    {
        $cases = [
            [150, 20],
            [250, 10],
            [400, 0],
        ];

        foreach ($cases as [$flightMinutes, $expectedDistanceScore]) {
            $destination = $this->makeDestination(['flight_minutes_from_vienna' => $flightMinutes]);

            $destination->monthlyWeather()->create([
                'month' => 6,
                'avg_temp' => 25,
                'min_temp' => 20,
                'max_temp' => 30,
                'rainy_days' => 1,
            ]);

            $results = $this->service->recommend($this->baseFilters(['distance' => '3h']));

            $expectedScore = 20 + 20 + $expectedDistanceScore + 10;
            $this->assertSame($expectedScore, $results->firstWhere('id', $destination->id)->score, "Failed for flight_minutes={$flightMinutes}");
        }
    }

    public function test_distance_score_for_the_5h_preference(): void
    {
        $cases = [
            [250, 20],
            [350, 0],
        ];

        foreach ($cases as [$flightMinutes, $expectedDistanceScore]) {
            $destination = $this->makeDestination(['flight_minutes_from_vienna' => $flightMinutes]);

            $destination->monthlyWeather()->create([
                'month' => 6,
                'avg_temp' => 25,
                'min_temp' => 20,
                'max_temp' => 30,
                'rainy_days' => 1,
            ]);

            $results = $this->service->recommend($this->baseFilters(['distance' => '5h']));

            $expectedScore = 20 + 20 + $expectedDistanceScore + 10;
            $this->assertSame($expectedScore, $results->firstWhere('id', $destination->id)->score, "Failed for flight_minutes={$flightMinutes}");
        }
    }

    public function test_days_score_within_and_outside_the_supported_range(): void
    {
        $cases = [
            [5, 10],  // within [3, 10]
            [12, 5],  // 2 days over -> small penalty
            [15, 0],  // 5 days over -> no match
            [1, 5],   // 2 days under -> small penalty
        ];

        foreach ($cases as [$days, $expectedDaysScore]) {
            $destination = $this->makeDestination(['min_days' => 3, 'max_days' => 10]);
            $destination->monthlyWeather()->create([
                'month' => 6,
                'avg_temp' => 25,
                'min_temp' => 20,
                'max_temp' => 30,
                'rainy_days' => 1,
            ]);

            $results = $this->service->recommend($this->baseFilters(['days' => $days]));

            $expectedScore = 20 + 20 + 15 + $expectedDaysScore;
            $this->assertSame($expectedScore, $results->firstWhere('id', $destination->id)->score, "Failed for days={$days}");
        }
    }

    public function test_perfect_match_scores_exactly_100(): void
    {
        $beach = VacationType::factory()->create(['code' => 'beach']);

        $destination = $this->makeDestination([
            'flight_minutes_from_vienna' => 120,
            'min_days' => 3,
            'max_days' => 10,
        ]);
        $destination->vacationTypes()->attach($beach->id);
        $destination->monthlyWeather()->create([
            'month' => 6,
            'avg_temp' => 25,
            'min_temp' => 20,
            'max_temp' => 30,
            'rainy_days' => 1,
        ]);

        $results = $this->service->recommend($this->baseFilters([
            'types' => ['beach'],
            'temperature' => 'warm',
            'distance' => '3h',
            'days' => 5,
        ]));

        $this->assertSame(100, $results->first()->score);
    }

    public function test_destinations_with_a_zero_score_are_excluded(): void
    {
        VacationType::factory()->create(['code' => 'beach']);

        $destination = $this->makeDestination([
            'flight_minutes_from_vienna' => 400,
            'min_days' => 3,
            'max_days' => 10,
        ]);
        // No weather row for month 6, no matching vacation type -> every component scores 0.

        $results = $this->service->recommend($this->baseFilters([
            'types' => ['beach'],
            'distance' => '3h',
            'days' => 20,
        ]));

        $this->assertCount(0, $results);
    }

    public function test_results_are_sorted_by_score_descending_and_limited_to_ten(): void
    {
        foreach (range(1, 12) as $i) {
            $this->makeDestination([
                'name' => "Destination {$i}",
                'flight_minutes_from_vienna' => $i * 30,
            ]);
        }

        $results = $this->service->recommend($this->baseFilters(['distance' => '3h']));

        $this->assertCount(10, $results);
        $scores = $results->pluck('score')->all();
        $sortedScores = $scores;
        rsort($sortedScores);
        $this->assertSame($sortedScores, $scores);
    }
}
