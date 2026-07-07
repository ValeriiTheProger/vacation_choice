<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\Destination;
use App\Models\DestinationSearchLog;
use App\Models\SearchLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DestinationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }

    public function test_destinations_index_lists_only_active_destinations(): void
    {
        Destination::factory()->create(['name' => 'Active Spot', 'is_active' => true]);
        Destination::factory()->create(['name' => 'Inactive Spot', 'is_active' => false]);

        $response = $this->get(route('destinations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('destinations-index');
        $response->assertViewHas('destinations', function ($destinations) {
            return $destinations->count() === 1 && $destinations->first()->name === 'Active Spot';
        });
    }

    public function test_search_returns_results_and_logs_the_search(): void
    {
        $destination = Destination::factory()->create(['is_active' => true]);

        $response = $this->get(route('search.results', [
            'duration_days' => 5,
            'temperature' => 'jedno',
            'distance' => 'kdekolvek',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('search-results');

        $this->assertDatabaseCount('search_logs', 1);
        $this->assertDatabaseHas('search_logs', [
            'days' => 5,
            'temperature_preference' => 'jedno',
            'distance_preference' => 'kdekolvek',
        ]);
        $this->assertDatabaseHas('destination_search_logs', [
            'destination_id' => $destination->id,
        ]);
    }

    public function test_search_validates_required_fields(): void
    {
        $response = $this->get(route('search.results'));

        $response->assertSessionHasErrors(['duration_days', 'temperature', 'distance']);
    }

    public function test_show_displays_a_destination_without_calling_the_real_currency_api(): void
    {
        Http::fake([
            'api.frankfurter.dev/*' => Http::response(['rates' => ['USD' => 1.1]], 200),
        ]);

        $country = Country::factory()->create(['currency_code' => 'USD', 'iso_code' => 'USX']);
        $destination = Destination::factory()->create(['country_id' => $country->id]);

        $response = $this->get(route('destinations.show', ['destination' => $destination]));

        $response->assertStatus(200);
        $response->assertViewIs('destination-show');
        $response->assertViewHas('currencyRate', 1.1);

        Http::assertSent(fn ($request) => str_contains($request->url(), 'api.frankfurter.dev'));
    }

    public function test_compare_requires_exactly_two_destination_ids(): void
    {
        $destination = Destination::factory()->create();

        $response = $this->get(route('destinations.compare', [
            'destination_ids' => [$destination->id],
        ]));

        $response->assertSessionHasErrors(['destination_ids']);
    }

    public function test_compare_shows_both_destinations_side_by_side(): void
    {
        Http::fake([
            'api.frankfurter.dev/*' => Http::response(['rates' => ['USD' => 1.1]], 200),
        ]);

        $left = Destination::factory()->create(['name' => 'Left City']);
        $right = Destination::factory()->create(['name' => 'Right City']);

        $response = $this->get(route('destinations.compare', [
            'destination_ids' => [$left->id, $right->id],
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('destinations-compare');
        $response->assertViewHas('left', fn ($value) => $value->id === $left->id);
        $response->assertViewHas('right', fn ($value) => $value->id === $right->id);
    }
}
