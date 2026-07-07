<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Výsledky vyhľadávania | vacation-choice</title>
    @vite(['resources/css/app.css', 'resources/css/search-results.css', 'resources/js/app.js'])
</head>
<body class="results-page">
    <section class="results-hero" style="background-image: linear-gradient(rgba(7, 28, 44, 0.45), rgba(7, 28, 44, 0.65)), url('{{ asset('HomeBackground.png') }}');">
        <header class="topbar">
            <div class="shell topbar-inner">
                <a href="{{ route('home') }}" class="brand">vacation-choice</a>

                <nav class="menu">
                    <a href="{{ route('home') }}" class="menu-item active">Domov</a>
                    <a href="{{ route('statistics.index') }}" class="menu-item">Štatistika</a>
                </nav>

                <div class="top-actions">
                    <a href="{{ route('destinations.index') }}" class="ghost-btn active-header-btn">Destinácie</a>
                </div>
            </div>
        </header>

        <div class="shell results-hero-content">
            <h1>Výsledky vyhľadávania</h1>
            <p>Našli sme pre vás destinácie, ktoré najlepšie zodpovedajú vašim predstavám o dovolenke.</p>

            <div class="search-summary">
                <span>{{ $filters['travel_mode'] === 'range' ? ($filters['date_from'] . ' - ' . $filters['date_to']) : ($filters['travel_month'] ?: 'Termín') }}</span>
                <span>{{ $filters['duration_days'] }} dni</span>
                <span>{{ count($filters['holiday_types']) }} typov dovolenky</span>
                <span>{{ $filterOptions['temperature'][$filters['temperature']] ?? 'Teplota' }}</span>
                <span>{{ $filterOptions['distance'][$filters['distance']] ?? 'Vzdialenosť' }}</span>
            </div>
        </div>
    </section>

    <main class="results-main">
        <div class="shell results-layout">
            <aside class="filters-card">
                <div class="filters-head">
                    <h2>Upraviť vyhľadávanie</h2>
                    <a href="{{ route('search.results') }}" class="reset-link">Resetovať</a>
                </div>

                <form class="filters-form" method="GET" action="{{ route('search.results') }}">
                    <div class="field-group">
                        <label for="travelMonth">Kedy chcete cestovať?</label>
                        <input type="month" id="travelMonth" name="travel_month" value="{{ $filters['travel_month'] }}">
                    </div>

                    <div class="field-group">
                        <label for="durationDays">Ako dlho?</label>
                        <input type="number" id="durationDays" name="duration_days" min="1" max="60" value="{{ $filters['duration_days'] }}">
                    </div>

                    <div class="field-group">
                        <label>Typ dovolenky</label>
                        <div class="results-checks">
                            @foreach($filterOptions['holiday_types'] as $value => $label)
                                <label><input type="checkbox" name="holiday_type[]" value="{{ $value }}" @checked(in_array($value, $filters['holiday_types'], true))> {{ $label }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Preferovaná teplota</label>
                        <div class="results-radios">
                            @foreach($filterOptions['temperature'] as $value => $label)
                                <label><input type="radio" name="temperature" value="{{ $value }}" @checked($filters['temperature'] === $value)> {{ $label }}</label>
                            @endforeach
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Vzdialenosť z Viedne</label>
                        <div class="results-radios">
                            @foreach($filterOptions['distance'] as $value => $label)
                                <label><input type="radio" name="distance" value="{{ $value }}" @checked($filters['distance'] === $value)> {{ $label }}</label>
                            @endforeach
                        </div>
                    </div>

                    <button class="primary-btn" type="submit">Hľadať znova</button>
                </form>
            </aside>

            <section class="results-column">
                <div class="results-head">
                    <h2>Odporúčané destinácie</h2>
                    <form method="GET" action="{{ route('destinations.compare') }}" id="compareForm" class="compare-form">
                        <input type="hidden" name="month" value="{{ $filters['selected_month'] }}">
                        <button type="submit" class="primary-btn compare-btn" id="compareBtn" disabled>Porovnať (0/2)</button>
                    </form>
                </div>

                @forelse($results as $result)
                    <article class="result-card result-card-link">
                        <label class="compare-pick">
                            <input type="checkbox" class="compare-checkbox" name="destination_ids[]" value="{{ $result->id }}" form="compareForm">
                            <span>Porovnať</span>
                        </label>
                        <div class="result-media">
                            <a href="{{ route('destinations.show', ['destination' => $result, 'month' => $filters['selected_month'], 'date_from' => $filters['date_from'] ?: null, 'date_to' => $filters['date_to'] ?: null]) }}">
                                <img src="{{ asset($result->image_path) }}" alt="{{ $result->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('HomeBackground.png') }}';">
                            </a>
                        </div>
                        <div class="result-body">
                            <h3>
                                <a href="{{ route('destinations.show', ['destination' => $result, 'month' => $filters['selected_month'], 'date_from' => $filters['date_from'] ?: null, 'date_to' => $filters['date_to'] ?: null]) }}">
                                    {{ $result->name }}@if($result->country), {{ $result->country->name_sk }}@endif
                                </a>
                            </h3>
                            <p>{{ $result->short_description }}</p>
                        </div>
                        <div class="result-score">
                            <strong>{{ $result->score }}%</strong>
                            <span>zhoda</span>
                            <a class="result-detail-link" href="{{ route('destinations.show', ['destination' => $result, 'month' => $filters['selected_month'], 'date_from' => $filters['date_from'] ?: null, 'date_to' => $filters['date_to'] ?: null]) }}">Zobraziť detail</a>
                        </div>
                    </article>
                @empty
                    <article class="empty-state">
                        <h3>Zatiaľ bez výsledkov</h3>
                        <p>Štruktúra stránky je pripravená. Po napojení logiky vyhľadávania sa tu zobrazia odporúčané destinácie.</p>
                    </article>
                @endforelse
            </section>
        </div>
    </main>
    <script>
        (() => {
            const form = document.getElementById('compareForm');
            const compareBtn = document.getElementById('compareBtn');
            if (!form || !compareBtn) {
                return;
            }

            const checkboxes = Array.from(document.querySelectorAll('.compare-checkbox'));
            const syncCompareState = () => {
                const selected = checkboxes.filter((input) => input.checked);
                if (selected.length > 2) {
                    selected[selected.length - 1].checked = false;
                }
                const count = checkboxes.filter((input) => input.checked).length;
                compareBtn.textContent = `Porovnať (${count}/2)`;
                compareBtn.disabled = count !== 2;
            };

            checkboxes.forEach((input) => {
                input.addEventListener('change', syncCompareState);
            });

            syncCompareState();
        })();
    </script>
</body>
</html>







