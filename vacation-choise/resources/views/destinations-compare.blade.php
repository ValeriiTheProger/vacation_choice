<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Porovnanie destinácií | vacation-choice</title>
    @vite(['resources/css/app.css', 'resources/css/destinations-compare.css', 'resources/js/app.js'])
</head>
<body class="results-page">
    <section class="results-hero" style="background-image: linear-gradient(rgba(7, 28, 44, 0.52), rgba(7, 28, 44, 0.72)), url('{{ asset('HomeBackground.png') }}');">
        <header class="topbar">
            <div class="shell topbar-inner">
                <a href="{{ route('home') }}" class="brand">vacation-choice</a>
                <nav class="menu">
                    <a href="{{ route('home') }}" class="menu-item">Domov</a>
                    <a href="{{ route('statistics.index') }}" class="menu-item">Štatistika</a>
                </nav>
                <div class="top-actions">
                    <a href="{{ route('destinations.index') }}" class="ghost-btn active-header-btn">Destinácie</a>
                </div>
            </div>
        </header>

        <div class="shell results-hero-content">
            <h1>Porovnanie destinácií</h1>
            <p>Porovnanie pre mesiac {{ $selectedMonth }}.</p>
        </div>
    </section>

    <main class="results-main">
        <div class="shell compare-layout">
            <a class="reset-link compare-back" href="{{ url()->previous() }}">&larr; Späť na výsledky</a>

            <div class="compare-table-wrap">
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th>Kritérium</th>
                            <th>{{ $left->name }}</th>
                            <th>{{ $right->name }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Štát</td>
                            <td>
                                @if($leftFlagUrl)<img src="{{ $leftFlagUrl }}" alt="{{ $left->country?->iso_code }}" class="country-flag">@endif
                                {{ $left->country?->name_sk ?? '-' }}
                            </td>
                            <td>
                                @if($rightFlagUrl)<img src="{{ $rightFlagUrl }}" alt="{{ $right->country?->iso_code }}" class="country-flag">@endif
                                {{ $right->country?->name_sk ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td>Počasie ({{ $selectedMonth }}.)</td>
                            <td>
                                @if($leftWeather)
                                    Avg {{ $leftWeather->avg_temp }} °C, Min {{ $leftWeather->min_temp }} °C, Max {{ $leftWeather->max_temp }} °C
                                @else
                                    Bez dát
                                @endif
                            </td>
                            <td>
                                @if($rightWeather)
                                    Avg {{ $rightWeather->avg_temp }} °C, Min {{ $rightWeather->min_temp }} °C, Max {{ $rightWeather->max_temp }} °C
                                @else
                                    Bez dát
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Mena</td>
                            <td>{{ $left->country?->currency_code ?? '-' }}</td>
                            <td>{{ $right->country?->currency_code ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Kurz voči EUR</td>
                            <td>
                                @if(($left->country?->currency_code ?? 'EUR') === 'EUR')
                                    Krajina používa EUR
                                @elseif($leftCurrencyRate !== null)
                                    1 EUR = {{ number_format($leftCurrencyRate, 4) }} {{ $left->country?->currency_code }}
                                @else
                                    Nedostupné
                                @endif
                            </td>
                            <td>
                                @if(($right->country?->currency_code ?? 'EUR') === 'EUR')
                                    Krajina používa EUR
                                @elseif($rightCurrencyRate !== null)
                                    1 EUR = {{ number_format($rightCurrencyRate, 4) }} {{ $right->country?->currency_code }}
                                @else
                                    Nedostupné
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Typ destinácie</td>
                            <td>
                                <div class="chips-wrap">
                                    @forelse($left->vacationTypes as $type)
                                        <span class="type-chip">{{ $type->name }}</span>
                                    @empty
                                        <span class="muted">Bez typov</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <div class="chips-wrap">
                                    @forelse($right->vacationTypes as $type)
                                        <span class="type-chip">{{ $type->name }}</span>
                                    @empty
                                        <span class="muted">Bez typov</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>







