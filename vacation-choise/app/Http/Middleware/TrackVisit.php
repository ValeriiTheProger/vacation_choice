<?php

namespace App\Http\Middleware;

use App\Models\VisitLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && !$request->expectsJson()) {
            $ipHash = hash('sha256', ($request->ip() ?? 'unknown') . '|' . config('app.key'));
            $hour = (int) now()->format('G');

            $timePeriod = match (true) {
                $hour >= 6 && $hour < 15 => '06:00-15:00',
                $hour >= 15 && $hour < 21 => '15:00-21:00',
                $hour >= 21 => '21:00-24:00',
                default => '00:00-06:00',
            };

            VisitLog::create([
                'ip_hash' => $ipHash,
                'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                'time_period' => $timePeriod,
                'visited_at' => now(),
            ]);
        }

        return $next($request);
    }
}
