<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $destination->name }} | vacation-choice</title>
    @vite(['resources/css/app.css', 'resources/css/destination-show.css', 'resources/js/app.js'])
</head>
<body class="results-page destination-page">
    <section class="results-hero" style="background-image: linear-gradient(rgba(7, 28, 44, 0.52), rgba(7, 28, 44, 0.72)), url('{{ asset($destination->image_path ?: 'HomeBackground.png') }}');">
        <header class="topbar">
            <div class="shell topbar-inner">
                <a href="{{ route('home') }}" class="brand">vacation-choice</a>

                <nav class="menu">
                    <a href="{{ route('home') }}" class="menu-item active">Domov</a>
                    <a href="{{ route('search.results', request()->query()) }}" class="menu-item">Výsledky</a>
                </nav>

                <div class="top-actions">
                    <a href="{{ route('destinations.index') }}" class="ghost-btn active-header-btn">Destinácie</a>
                </div>
            </div>
        </header>

        <div class="shell destination-hero-content">
            <h1>{{ $destination->name }}@if($destination->country), {{ $destination->country->name_sk }}@endif</h1>
            <p>{{ $destination->short_description }}</p>
        </div>
    </section>

    <main class="results-main">
        <div class="shell destination-layout">
            <section class="destination-main-card">
                <img src="{{ asset($destination->image_path ?: 'HomeBackground.png') }}" alt="{{ $destination->name }}" class="destination-cover" onerror="this.onerror=null;this.src='{{ asset('HomeBackground.png') }}';">

                <div class="destination-content-grid">
                    <article class="destination-info-box">
                        <h2>Štát a základné informácie</h2>
                        <ul>
                            <li>
                                Krajina:
                                @if($flagUrl)
                                    <img src="{{ $flagUrl }}" alt="{{ $destination->country?->iso_code }}" class="country-flag" loading="lazy">
                                @endif
                                {{ $destination->country?->name_sk ?? '-' }}
                            </li>
                            <li>Hlavné mesto: {{ $destination->country?->capital ?? '-' }}</li>
                            <li>Let z Viedne: {{ $destination->flight_minutes_from_vienna }} min</li>
                            <li>Odporúčaná dĺžka: {{ $destination->min_days }}-{{ $destination->max_days }} dni</li>
                        </ul>
                    </article>

                    <article class="destination-info-box">
                        <h2>Typy dovolenky</h2>
                        <div class="chips-wrap">
                            @forelse($destination->vacationTypes as $type)
                                <span class="type-chip">{{ $type->name }}</span>
                            @empty
                                <span class="muted">Zatiaľ bez typov</span>
                            @endforelse
                        </div>
                    </article>
                </div>

                <div class="destination-content-grid">
                    <article class="destination-info-box">
                        <h2>Mena a kurz</h2>
                        <ul>
                            <li>Mena: {{ $destination->country?->currency_code ?? '-' }}</li>
                            @if(($destination->country?->currency_code ?? 'EUR') !== 'EUR')
                                <li>
                                    Kurz voči EUR:
                                    @if($currencyRate !== null)
                                        1 EUR = {{ number_format($currencyRate, 4) }} {{ $destination->country?->currency_code }}
                                    @else
                                        momentálne nedostupný
                                    @endif
                                </li>
                            @endif
                        </ul>
                    </article>

                    <article class="destination-info-box reason-box">
                        <h2>Prečo práve teraz</h2>
                        <p>{{ $whyNowText }}</p>
                    </article>
                </div>

                <article class="destination-info-box weather-box">
                    <h2>Počasie pre vybraný mesiac ({{ $selectedMonth }}.)</h2>
                    @if($weather)
                        <div class="weather-grid">
                            <div class="weather-card">
                                <strong>{{ $weather->month }}.</strong>
                                <span>Avg: {{ $weather->avg_temp }} °C</span>
                                <span>Min: {{ $weather->min_temp }} °C</span>
                                <span>Max: {{ $weather->max_temp }} °C</span>
                                <span>Daždivé dni: {{ $weather->rainy_days }}</span>
                            </div>
                        </div>
                    @else
                        <p class="muted">Pre vybraný mesiac zatiaľ nemáme počasie.</p>
                    @endif
                </article>
            </section>
        </div>
    </main>
</body>
</html>






