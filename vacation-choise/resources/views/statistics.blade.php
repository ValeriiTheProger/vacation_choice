<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Štatistiky | vacation-choice</title>
    @vite(['resources/css/app.css', 'resources/css/statistics.css', 'resources/js/app.js'])
</head>
<body class="results-page">
    <section class="results-hero" style="background-image: linear-gradient(rgba(7, 28, 44, 0.45), rgba(7, 28, 44, 0.65)), url('{{ asset('HomeBackground.png') }}');">
        <header class="topbar">
            <div class="shell topbar-inner">
                <a href="{{ route('home') }}" class="brand">vacation-choice</a>
                <nav class="menu">
                    <a href="{{ route('home') }}" class="menu-item">Domov</a>
                    <a href="{{ route('statistics.index') }}" class="menu-item active">Štatistika</a>
                </nav>
                <div class="top-actions">
                    <a href="{{ route('destinations.index') }}" class="ghost-btn active-header-btn">Destinácie</a>
                </div>
            </div>
        </header>

        <div class="shell results-hero-content">
            <h1>Štatistiky používania</h1>
            <p>Prehľad návštevnosti, vyhľadávania a preferencií návštevníkov portálu.</p>
        </div>
    </section>

    <main class="results-main">
        <div class="shell stats-layout">
            <section class="results-column">
                <div class="stats-counters">
                    <article class="stats-counter-card">
                        <h3>Celkové návštevy</h3>
                        <p>{{ $totalVisits }}</p>
                    </article>
                    <article class="stats-counter-card">
                        <h3>Unikátne návštevy</h3>
                        <p>{{ $uniqueVisits }}</p>
                    </article>
                </div>

                <article class="destination-info-box">
                    <h2>Návštevnosť podľa dennej doby</h2>
                    @php($maxPeriod = max($periodCounts ?: [1]))
                    <div class="bars-grid">
                        @foreach($periodCounts as $label => $value)
                            <div class="bar-row">
                                <span class="bar-label">{{ $label }}</span>
                                <div class="bar-track"><span class="bar-fill" style="width: {{ $maxPeriod > 0 ? ($value / $maxPeriod) * 100 : 0 }}%"></span></div>
                                <span class="bar-value">{{ $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="destination-info-box">
                    <h2>Čo ľudia hľadajú</h2>
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th><a href="{{ route('statistics.index', ['sort' => 'destination', 'dir' => $sort === 'destination' && $dir === 'asc' ? 'desc' : 'asc']) }}">Destinácia</a></th>
                                <th><a href="{{ route('statistics.index', ['sort' => 'country', 'dir' => $sort === 'country' && $dir === 'asc' ? 'desc' : 'asc']) }}">Štát</a></th>
                                <th><a href="{{ route('statistics.index', ['sort' => 'count', 'dir' => $sort === 'count' && $dir === 'asc' ? 'desc' : 'asc']) }}">Počet vyhľadávaní</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($searchedDestinations as $row)
                                <tr>
                                    <td>{{ $row->destination_name }}</td>
                                    <td>{{ $row->country_name }}</td>
                                    <td>{{ $row->searches }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3">Zatiaľ bez dát.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </article>

                <article class="destination-info-box">
                    <h2>Preferencie návštevníkov</h2>
                    <h3 class="stats-subtitle">Typy dovolenky</h3>
                    <div class="stats-chart-wrap">
                        <canvas id="vacationTypesChart" aria-label="Typy dovolenky graf"></canvas>
                    </div>

                    <h3 class="stats-subtitle">Klimatické preferencie</h3>
                    <div class="stats-chart-wrap">
                        <canvas id="temperatureChart" aria-label="Klimatické preferencie graf"></canvas>
                    </div>
                </article>
            </section>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            const typeLabels = @json(array_keys($typeCounts));
            const typeData = @json(array_values($typeCounts));
            const tempLabels = @json(array_keys($temperatureCounts));
            const tempData = @json(array_values($temperatureCounts));

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                    },
                },
            };

            const typesCtx = document.getElementById('vacationTypesChart');
            if (typesCtx) {
                new Chart(typesCtx, {
                    type: 'bar',
                    data: {
                        labels: typeLabels,
                        datasets: [{
                            data: typeData,
                            backgroundColor: '#4f9f6e',
                            borderRadius: 6,
                        }],
                    },
                    options: commonOptions,
                });
            }

            const tempCtx = document.getElementById('temperatureChart');
            if (tempCtx) {
                new Chart(tempCtx, {
                    type: 'bar',
                    data: {
                        labels: tempLabels,
                        datasets: [{
                            data: tempData,
                            backgroundColor: '#3d7eb6',
                            borderRadius: 6,
                        }],
                    },
                    options: commonOptions,
                });
            }
        })();
    </script>
</body>
</html>






