<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Destinácie | vacation-choice</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="results-page">
    <section class="results-hero" style="background-image: linear-gradient(rgba(7, 28, 44, 0.45), rgba(7, 28, 44, 0.65)), url('{{ asset('HomeBackground.png') }}');">
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
            <h1>Všetky destinácie</h1>
            <p>Vyberte mesto a pozrite si detailne informacie.</p>
        </div>
    </section>

    <main class="results-main">
        <div class="shell destinations-grid">
            @foreach($destinations as $destination)
                <a class="destination-list-card" href="{{ route('destinations.show', ['destination' => $destination, 'month' => now()->month]) }}">
                    <img src="{{ asset($destination->image_path ?: 'HomeBackground.png') }}" alt="{{ $destination->name }}" onerror="this.onerror=null;this.src='{{ asset('HomeBackground.png') }}';">
                    <div class="destination-list-body">
                        <h3>{{ $destination->name }}</h3>
                        <p>{{ $destination->country?->name_sk }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </main>
</body>
</html>



