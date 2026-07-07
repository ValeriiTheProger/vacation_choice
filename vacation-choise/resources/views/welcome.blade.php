<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>vacation-choice</title>
    @vite(['resources/css/app.css', 'resources/css/home.css', 'resources/js/app.js'])
</head>
<body>
    <div class="page-bg" style="background-image: linear-gradient(rgba(7, 12, 19, 0.42), rgba(7, 12, 19, 0.58)), url('{{ asset('HomeBackground.png') }}');"></div>

    <header class="topbar">
        <div class="shell topbar-inner">
            <a href="#" class="brand">vacation-choice</a>

            <nav class="menu">
                <a href="{{ route('home') }}" class="menu-item active">Domov</a>
                <a href="{{ route('statistics.index') }}" class="menu-item">Štatistika</a>
            </nav>

            <div class="top-actions">
                <a href="{{ route('destinations.index') }}" class="ghost-btn active-header-btn">Destinácie</a>
            </div>
        </div>
    </header>

    <main class="hero-wrap">
        <section class="shell hero-panel">
            <div class="planner">
                <h1>Hej, kam chceš ísť na dovolenku?</h1>
                <p>Vyplň formulár a nájdi destinácie podľa tvojich preferencií.</p>

                <form class="travel-form" id="travelForm" method="GET" action="{{ route('search.results') }}" novalidate>
                    <div class="field-group">
                        <label>Kedy chceš cestovať?</label>
                        <div class="choice-row">
                            <label><input type="radio" name="travel_mode" value="month" checked> Mesiac</label>
                            <label><input type="radio" name="travel_mode" value="range"> Dátumový rozsah</label>
                        </div>
                        <div class="choice-grid">
                            <input type="month" id="travelMonth" name="travel_month">
                            <div class="date-range hidden" id="dateRangeWrap">
                                <input type="date" id="dateFrom" name="date_from">
                                <input type="date" id="dateTo" name="date_to">
                            </div>
                        </div>
                        <p class="error-text" data-error-for="travel_mode"></p>
                    </div>

                    <div class="field-group">
                        <label for="durationDays">Ako dlho (počet dní)</label>
                        <input type="number" id="durationDays" name="duration_days" min="1" max="60" placeholder="Napr. 7">
                        <p class="error-text" data-error-for="duration_days"></p>
                    </div>

                    <div class="field-group">
                        <label>Čo hľadáš? (môžeš vybrať viac možností)</label>
                        <div class="checks-grid">
                            <label><input type="checkbox" name="holiday_type[]" value="more-a-plaz"> More a pláž</label>
                            <label><input type="checkbox" name="holiday_type[]" value="hory-a-priroda"> Hory a príroda</label>
                            <label><input type="checkbox" name="holiday_type[]" value="historicke-mesta"> Historické mestá</label>
                            <label><input type="checkbox" name="holiday_type[]" value="mestsky-vylet"> Mestský výlet</label>
                            <label><input type="checkbox" name="holiday_type[]" value="aktivity-a-dobrodruzstvo"> Aktivity a dobrodružstvo</label>
                        </div>
                        <p class="error-text" data-error-for="holiday_type"></p>
                    </div>

                    <div class="field-group">
                        <label for="tempPreference">Preferovaná teplota</label>
                        <select id="tempPreference" name="temperature">
                            <option value="">Vyber možnosť</option>
                            <option value="horuco">Horúco (30 °C+)</option>
                            <option value="teplo">Teplo (20-29 °C)</option>
                            <option value="prijemne">Príjemne (10-19 °C)</option>
                            <option value="jedno">Jedno mi to</option>
                        </select>
                        <p class="error-text" data-error-for="temperature"></p>
                    </div>

                    <div class="field-group">
                        <label>Vzdialenosť z Viedne</label>
                        <div class="choice-row">
                            <label><input type="radio" name="distance" value="do-3h"> Do 3 hodín letu</label>
                            <label><input type="radio" name="distance" value="do-5h"> Do 5 hodín letu</label>
                            <label><input type="radio" name="distance" value="kdekolvek"> Kdekoľvek</label>
                        </div>
                        <p class="error-text" data-error-for="distance"></p>
                    </div>

                    <button class="primary-btn" type="submit">HĽADAŤ DOVOLENKU</button>
                    <p class="success-text" id="successText"></p>
                </form>
            </div>
        </section>
    </main>

    <script>
        (() => {
            const form = document.getElementById('travelForm');
            const monthInput = document.getElementById('travelMonth');
            const dateFrom = document.getElementById('dateFrom');
            const dateTo = document.getElementById('dateTo');
            const durationDaysInput = document.getElementById('durationDays');
            const dateRangeWrap = document.getElementById('dateRangeWrap');
            const successText = document.getElementById('successText');

            const setTravelMode = () => {
                const mode = form.querySelector('input[name="travel_mode"]:checked')?.value;
                if (mode === 'range') {
                    dateRangeWrap.classList.remove('hidden');
                    monthInput.classList.add('hidden');
                } else {
                    dateRangeWrap.classList.add('hidden');
                    monthInput.classList.remove('hidden');
                }
            };

            const setError = (key, message) => {
                const el = form.querySelector(`[data-error-for="${key}"]`);
                if (el) {
                    el.textContent = message;
                    el.classList.add('visible');
                }
            };

            const clearErrors = () => {
                form.querySelectorAll('.error-text').forEach((el) => {
                    el.textContent = '';
                    el.classList.remove('visible');
                });
                successText.textContent = '';
            };

            const syncDurationFromRange = () => {
                const mode = form.querySelector('input[name="travel_mode"]:checked')?.value;
                if (mode !== 'range' || !dateFrom.value || !dateTo.value) {
                    return;
                }

                const from = new Date(dateFrom.value);
                const to = new Date(dateTo.value);
                const diff = to.getTime() - from.getTime();
                if (Number.isNaN(diff) || diff < 0) {
                    return;
                }

                const days = Math.floor(diff / (1000 * 60 * 60 * 24)) + 1;
                durationDaysInput.value = String(days);
            };

            form.querySelectorAll('input[name="travel_mode"]').forEach((radio) => {
                radio.addEventListener('change', () => {
                    setTravelMode();
                    syncDurationFromRange();
                });
            });

            dateFrom.addEventListener('change', syncDurationFromRange);
            dateTo.addEventListener('change', syncDurationFromRange);

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                clearErrors();
                setTravelMode();

                let isValid = true;
                const mode = form.querySelector('input[name="travel_mode"]:checked')?.value;
                syncDurationFromRange();
                const duration = form.duration_days.value.trim();
                const selectedTypes = form.querySelectorAll('input[name="holiday_type[]"]:checked');
                const temp = form.temperature.value;
                const distance = form.querySelector('input[name="distance"]:checked');

                if (mode === 'month' && !monthInput.value) {
                    setError('travel_mode', 'Vyber mesiac cestovania.');
                    isValid = false;
                }

                if (mode === 'range') {
                    if (!dateFrom.value || !dateTo.value) {
                        setError('travel_mode', 'Vyplň dátum odchodu aj návratu.');
                        isValid = false;
                    } else if (dateFrom.value > dateTo.value) {
                        setError('travel_mode', 'Dátum odchodu musí byť skôr ako dátum návratu.');
                        isValid = false;
                    }
                }

                if (!duration || Number(duration) < 1) {
                    setError('duration_days', 'Zadaj platný počet dní (minimálne 1).');
                    isValid = false;
                }

                if (selectedTypes.length === 0) {
                    setError('holiday_type', 'Vyber aspoň jeden typ dovolenky.');
                    isValid = false;
                }

                if (!temp) {
                    setError('temperature', 'Vyber preferovanú teplotu.');
                    isValid = false;
                }

                if (!distance) {
                    setError('distance', 'Vyber preferovanú vzdialenosť letu.');
                    isValid = false;
                }

                if (isValid) {
                    form.submit();
                }
            });

            setTravelMode();
        })();
    </script>
</body>
</html>







