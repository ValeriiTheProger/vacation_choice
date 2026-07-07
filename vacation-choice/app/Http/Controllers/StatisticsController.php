<?php

namespace App\Http\Controllers;

use App\Models\SearchLog;
use App\Models\VisitLog;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $totalVisits = VisitLog::count();
        $uniqueVisits = $this->countUniqueVisits();

        $periodCountsRaw = VisitLog::select('time_period', DB::raw('COUNT(*) as total'))
            ->groupBy('time_period')
            ->pluck('total', 'time_period')
            ->all();

        $periodCounts = [
            '06:00-15:00' => (int) ($periodCountsRaw['06:00-15:00'] ?? 0),
            '15:00-21:00' => (int) ($periodCountsRaw['15:00-21:00'] ?? 0),
            '21:00-24:00' => (int) ($periodCountsRaw['21:00-24:00'] ?? 0),
            '00:00-06:00' => (int) ($periodCountsRaw['00:00-06:00'] ?? 0),
        ];

        $sort = request('sort', 'count');
        $dir = request('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $searchesQuery = DB::table('destination_search_logs as dsl')
            ->join('destinations as d', 'd.id', '=', 'dsl.destination_id')
            ->join('countries as c', 'c.id', '=', 'd.country_id')
            ->selectRaw('d.id, d.name as destination_name, c.name_sk as country_name, COUNT(*) as searches')
            ->groupBy('d.id', 'd.name', 'c.name_sk');

        if ($sort === 'destination') {
            $searchesQuery->orderBy('d.name', $dir);
        } elseif ($sort === 'country') {
            $searchesQuery->orderBy('c.name_sk', $dir)->orderBy('d.name', 'asc');
        } else {
            $searchesQuery->orderBy('searches', $dir);
        }

        $searchedDestinations = $searchesQuery->get();

        $temperatureRaw = SearchLog::select('temperature_preference', DB::raw('COUNT(*) as total'))
            ->groupBy('temperature_preference')
            ->pluck('total', 'temperature_preference')
            ->all();

        $temperatureMap = [
            'horuco' => 'Horúco (30 °C+)',
            'teplo' => 'Teplo (20-29 °C)',
            'prijemne' => 'Príjemne (10-19 °C)',
            'jedno' => 'Jedno mi to',
        ];

        $temperatureCounts = [];
        foreach ($temperatureMap as $key => $label) {
            $temperatureCounts[$label] = (int) ($temperatureRaw[$key] ?? 0);
        }

        $typeMap = [
            'more-a-plaz' => 'More a pláž',
            'hory-a-priroda' => 'Hory a príroda',
            'historicke-mesta' => 'Historické mestá',
            'mestsky-vylet' => 'Mestský výlet',
            'aktivity-a-dobrodruzstvo' => 'Aktivity a dobrodružstvo',
        ];

        $typeCountsRaw = array_fill_keys(array_keys($typeMap), 0);
        foreach (SearchLog::select('vacation_types')->get() as $log) {
            foreach ((array) $log->vacation_types as $typeCode) {
                if (array_key_exists($typeCode, $typeCountsRaw)) {
                    $typeCountsRaw[$typeCode]++;
                }
            }
        }

        $typeCounts = [];
        foreach ($typeMap as $key => $label) {
            $typeCounts[$label] = (int) $typeCountsRaw[$key];
        }

        return view('statistics', [
            'totalVisits' => $totalVisits,
            'uniqueVisits' => $uniqueVisits,
            'periodCounts' => $periodCounts,
            'searchedDestinations' => $searchedDestinations,
            'temperatureCounts' => $temperatureCounts,
            'typeCounts' => $typeCounts,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    private function countUniqueVisits(): int
    {
        $logs = VisitLog::orderBy('ip_hash')->orderBy('visited_at')->get(['ip_hash', 'visited_at']);

        $lastVisitByIp = [];
        $unique = 0;

        foreach ($logs as $log) {
            $ip = $log->ip_hash;
            $current = $log->visited_at;

            if (!isset($lastVisitByIp[$ip]) || $current->diffInMinutes($lastVisitByIp[$ip], absolute: true) > 60) {
                $unique++;
            }

            $lastVisitByIp[$ip] = $current;
        }

        return $unique;
    }
}
