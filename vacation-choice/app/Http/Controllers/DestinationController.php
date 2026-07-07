<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\DestinationSearchLog;
use App\Models\SearchLog;
use App\Services\DestinationRecommendationService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DestinationController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'filterOptions' => [
                'holiday_types' => array_map(fn (array $type) => $type['label'], config('vacation.holiday_types')),
                'temperature' => array_map(fn (array $type) => $type['label'], config('vacation.temperatures')),
                'distance' => array_map(fn (array $type) => $type['label'], config('vacation.distances')),
            ],
        ]);
    }

    public function all()
    {
        $destinations = Destination::query()
            ->with('country')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('destinations-index', [
            'destinations' => $destinations,
        ]);
    }

    public function search(Request $request, DestinationRecommendationService $service)
    {
        $holidayTypes = config('vacation.holiday_types');
        $temperatures = config('vacation.temperatures');
        $distances = config('vacation.distances');

        $input = $request->validate([
            'travel_mode' => ['nullable', 'in:month,range'],
            'travel_month' => ['nullable', 'date_format:Y-m'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:60'],
            'holiday_type' => ['nullable', 'array'],
            'holiday_type.*' => ['string'],
            'temperature' => ['required', 'in:'.implode(',', array_keys($temperatures))],
            'distance' => ['required', 'in:'.implode(',', array_keys($distances))],
        ]);

        $month = (int) now()->month;
        if (!empty($input['travel_month'])) {
            $month = (int) substr($input['travel_month'], 5, 2);
        }

        $serviceFilters = [
            'month' => $month,
            'days' => (int) $input['duration_days'],
            'types' => array_values(array_filter(array_map(
                fn (string $value) => $holidayTypes[$value]['code'] ?? null,
                $input['holiday_type'] ?? []
            ))),
            'temperature' => $temperatures[$input['temperature']]['code'],
            'distance' => $distances[$input['distance']]['code'],
        ];

        $results = $service->recommend($serviceFilters);

        $searchLog = SearchLog::create([
            'month' => $month,
            'days' => (int) $input['duration_days'],
            'temperature_preference' => $input['temperature'],
            'distance_preference' => $input['distance'],
            'vacation_types' => $input['holiday_type'] ?? [],
            'searched_at' => now(),
        ]);

        foreach ($results as $destination) {
            DestinationSearchLog::create([
                'search_log_id' => $searchLog->id,
                'destination_id' => $destination->id,
                'score' => (float) ($destination->score ?? 0),
            ]);
        }

        $filters = [
            'travel_mode' => $input['travel_mode'] ?? 'month',
            'travel_month' => $input['travel_month'] ?? '',
            'selected_month' => $month,
            'date_from' => $input['date_from'] ?? '',
            'date_to' => $input['date_to'] ?? '',
            'duration_days' => (int) $input['duration_days'],
            'holiday_types' => $input['holiday_type'] ?? [],
            'temperature' => $input['temperature'],
            'distance' => $input['distance'],
        ];

        $filterOptions = [
            'holiday_types' => array_map(fn (array $type) => $type['label'], $holidayTypes),
            'temperature' => array_map(fn (array $type) => $type['label'], $temperatures),
            'distance' => array_map(fn (array $type) => $type['label'], $distances),
        ];

        return view('search-results', [
            'results' => $results,
            'filters' => $filters,
            'filterOptions' => $filterOptions,
        ]);
    }

    public function show(Request $request, Destination $destination)
    {
        $selectedMonth = max(1, min(12, (int) $request->integer('month', now()->month)));

        $destination->load([
            'country',
            'vacationTypes',
            'monthlyWeather' => fn ($query) => $query->where('month', $selectedMonth),
        ]);

        $weather = $destination->monthlyWeather->first();
        $currencyRate = $this->fetchCurrencyRateFromEur($destination->country?->currency_code);
        $whyNowText = $this->buildWhyNowText($destination, $weather, $selectedMonth);
        $flagUrl = $destination->country?->iso_code
            ? 'https://www.geonames.org/flags/x/' . strtolower($destination->country->iso_code) . '.gif'
            : null;

        return view('destination-show', [
            'destination' => $destination,
            'selectedMonth' => $selectedMonth,
            'weather' => $weather,
            'currencyRate' => $currencyRate,
            'whyNowText' => $whyNowText,
            'flagUrl' => $flagUrl,
        ]);
    }

    public function compare(Request $request)
    {
        $input = $request->validate([
            'destination_ids' => ['required', 'array', 'size:2'],
            'destination_ids.*' => ['integer', 'distinct', 'exists:destinations,id'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $selectedMonth = (int) ($input['month'] ?? now()->month);

        $destinations = Destination::query()
            ->with([
                'country',
                'vacationTypes',
                'monthlyWeather' => fn ($query) => $query->where('month', $selectedMonth),
            ])
            ->whereIn('id', $input['destination_ids'])
            ->get()
            ->sortBy(fn (Destination $destination) => array_search($destination->id, $input['destination_ids'], true))
            ->values();

        if ($destinations->count() !== 2) {
            return redirect()->route('search.results')->withErrors([
                'destination_ids' => 'Vyberte presne dve destinácie na porovnanie.',
            ]);
        }

        $left = $destinations[0];
        $right = $destinations[1];

        return view('destinations-compare', [
            'left' => $left,
            'right' => $right,
            'selectedMonth' => $selectedMonth,
            'leftWeather' => $left->monthlyWeather->first(),
            'rightWeather' => $right->monthlyWeather->first(),
            'leftCurrencyRate' => $this->fetchCurrencyRateFromEur($left->country?->currency_code),
            'rightCurrencyRate' => $this->fetchCurrencyRateFromEur($right->country?->currency_code),
            'leftFlagUrl' => $left->country?->iso_code ? 'https://www.geonames.org/flags/x/' . strtolower($left->country->iso_code) . '.gif' : null,
            'rightFlagUrl' => $right->country?->iso_code ? 'https://www.geonames.org/flags/x/' . strtolower($right->country->iso_code) . '.gif' : null,
        ]);
    }

    private function fetchCurrencyRateFromEur(?string $currencyCode): ?float
    {
        if (!$currencyCode || strtoupper($currencyCode) === 'EUR') {
            return null;
        }

        try {
            $response = Http::timeout(10)->get('https://api.frankfurter.dev/v1/latest', [
                'base' => 'EUR',
                'symbols' => strtoupper($currencyCode),
            ]);
        } catch (ConnectionException $e) {
            if (!str_contains($e->getMessage(), 'cURL error 60')) {
                return null;
            }

            $response = Http::withoutVerifying()->timeout(10)->get('https://api.frankfurter.dev/v1/latest', [
                'base' => 'EUR',
                'symbols' => strtoupper($currencyCode),
            ]);
        }

        if (!$response->ok()) {
            return null;
        }

        $rate = $response->json('rates.' . strtoupper($currencyCode));
        return is_numeric($rate) ? (float) $rate : null;
    }

    private function buildWhyNowText(Destination $destination, $weather, int $selectedMonth): string
    {
        $avgTempText = $weather ? $weather->avg_temp . ' °C' : 'bez dostupnej teploty';
        $flightHours = round($destination->flight_minutes_from_vienna / 60, 1);
        $types = $destination->vacationTypes->pluck('name')->take(2)->implode(' a ');
        $typesText = $types !== '' ? $types : 'rôzne typy dovolenky';

        if ($weather && $weather->avg_temp >= 25) {
            $climateText = 'teplé a slnečné počasie';
        } elseif ($weather && $weather->avg_temp >= 15) {
            $climateText = 'príjemné počasie na objavovanie';
        } elseif ($weather) {
            $climateText = 'chladnejšie, ale stále vhodné počasie';
        } else {
            $climateText = 'obdobie vhodné podľa vašich preferencií';
        }

        return "V mesiaci {$selectedMonth}. má destinácia priemerne {$avgTempText}, čo prináša {$climateText}. "
            . "Let z Viedne trvá približne {$flightHours} h a lokalita sa hodí najmä na {$typesText}.";
    }
}


