<?php

namespace Tests\Feature;

use App\Http\Middleware\TrackVisit;
use App\Models\VisitLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_page_loads_successfully(): void
    {
        $response = $this->get(route('statistics.index'));

        $response->assertStatus(200);
        $response->assertViewIs('statistics');
    }

    public function test_statistics_counts_total_and_unique_visits(): void
    {
        VisitLog::create([
            'ip_hash' => 'hash-a',
            'user_agent' => 'Test Agent',
            'time_period' => '06:00-15:00',
            'visited_at' => now()->subHours(3),
        ]);
        VisitLog::create([
            'ip_hash' => 'hash-a',
            'user_agent' => 'Test Agent',
            'time_period' => '06:00-15:00',
            'visited_at' => now(),
        ]);
        VisitLog::create([
            'ip_hash' => 'hash-b',
            'user_agent' => 'Test Agent',
            'time_period' => '15:00-21:00',
            'visited_at' => now(),
        ]);

        $response = $this->withoutMiddleware(TrackVisit::class)->get(route('statistics.index'));

        $response->assertStatus(200);
        $response->assertViewHas('totalVisits', 3);
        // hash-a's two visits are more than 60 minutes apart, so they count as 2 unique visits, plus hash-b's one.
        $response->assertViewHas('uniqueVisits', 3);
    }
}
