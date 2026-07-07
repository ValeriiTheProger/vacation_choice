<?php

namespace App\Services;

use App\Models\Destination;
use Illuminate\Support\Collection;

class DestinationRecommendationService
{
    public function recommend(array $filters): Collection
    {
        $month = (int) $filters['month'];
        $days = (int) $filters['days'];
        $selectedTypes = $filters['types'] ?? [];
        $temperature = $filters['temperature'];
        $distance = $filters['distance'];

        $destinations = Destination::with(['country', 'vacationTypes', 'monthlyWeather'])
            ->where('is_active', true)
            ->get();

        return $destinations
            ->map(function (Destination $destination) use ($month, $days, $selectedTypes, $temperature, $distance) {
                $score = 0.0;

                $destinationTypeCodes = $destination->vacationTypes->pluck('code')->all();
                $matchedTypes = array_intersect($selectedTypes, $destinationTypeCodes);

                if (count($selectedTypes) > 0) {
                    $score += count($matchedTypes) / count($selectedTypes) * 40;
                } else {
                    $score += 20;
                }

                $weather = $destination->monthlyWeather->firstWhere('month', $month);
                if ($weather) {
                    $score += $this->calculateTemperatureScore($temperature, (float) $weather->avg_temp);
                }

                $score += $this->calculateDistanceScore($distance, (int) $destination->flight_minutes_from_vienna);
                $score += $this->calculateDaysScore($days, (int) $destination->min_days, (int) $destination->max_days);

                $destination->score = min(100, (int) round($score));
                $destination->selected_month_weather = $weather;

                return $destination;
            })
            ->filter(fn (Destination $destination) => $destination->score > 0)
            ->sortByDesc('score')
            ->take(10)
            ->values();
    }

    private function calculateTemperatureScore(string $preference, float $avgTemp): int
    {
        if ($preference === 'any') {
            return 20;
        }

        $ranges = [
            'hot' => ['min' => 30, 'max' => 100],
            'warm' => ['min' => 20, 'max' => 29],
            'mild' => ['min' => 10, 'max' => 19],
        ];

        $range = $ranges[$preference];

        if ($avgTemp >= $range['min'] && $avgTemp <= $range['max']) {
            return 30;
        }

        $difference = $avgTemp < $range['min'] ? $range['min'] - $avgTemp : $avgTemp - $range['max'];

        if ($difference <= 3) {
            return 20;
        }

        if ($difference <= 6) {
            return 10;
        }

        return 0;
    }

    private function calculateDistanceScore(string $preference, int $flightMinutes): int
    {
        if ($preference === '3h') {
            if ($flightMinutes <= 180) {
                return 20;
            }

            if ($flightMinutes <= 300) {
                return 10;
            }

            return 0;
        }

        if ($preference === '5h') {
            return $flightMinutes <= 300 ? 20 : 0;
        }

        return 15;
    }

    private function calculateDaysScore(int $days, int $minDays, int $maxDays): int
    {
        if ($days >= $minDays && $days <= $maxDays) {
            return 10;
        }

        $difference = $days < $minDays ? $minDays - $days : $days - $maxDays;

        return $difference <= 2 ? 5 : 0;
    }
}
