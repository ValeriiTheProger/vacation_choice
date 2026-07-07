# Vacation Choice

[![Tests](https://github.com/ValeriiTheProger/vacation_choice/actions/workflows/tests.yml/badge.svg)](https://github.com/ValeriiTheProger/vacation_choice/actions/workflows/tests.yml)

A Laravel recommendation engine that suggests vacation destinations based on
travel month, trip length, preferred vacation type, temperature range, and
flight distance. Built as a learning project to practice server-side scoring
logic, external API integration, and Laravel testing conventions.

<!-- Screenshots: add homepage / search-results / statistics screenshots here -->

## Features

- **Guided search** — pick a travel month (or date range), trip length,
  vacation type(s), preferred temperature, and flight distance from Vienna.
- **Weighted recommendation scoring** — destinations are ranked by how well
  they match the selected filters (see [Recommendation scoring](#recommendation-scoring) below).
- **Destination comparison** — compare two destinations side by side,
  including live currency conversion and weather for a chosen month.
- **Usage statistics dashboard** — visit counts, most-searched destinations,
  and aggregated search preferences.
- **Visit tracking middleware** — logs page visits (hashed IP, time-of-day
  bucket) without storing raw IP addresses.

## Tech Stack

- **Backend:** PHP 8.3, Laravel 13
- **Database:** MySQL (SQLite in-memory for the test suite)
- **Frontend:** Blade templates, Vite, vanilla CSS/JS
- **Testing:** PHPUnit (Laravel feature & service tests)
- **External APIs:** [Frankfurter](https://www.frankfurter.dev/) (currency rates), [GeoNames](https://www.geonames.org/flags/) (country flags)

## Key Technical Decisions

### Recommendation scoring

`DestinationRecommendationService::recommend()` scores every active
destination against the user's filters and returns the top 10, highest score
first. The score is a weighted sum of four independent components:

| Component | Weight | Logic |
|---|---|---|
| Vacation type match | 40 | Share of selected types the destination offers (all selected types matched = full 40; no type filter selected = flat 20) |
| Temperature fit | 30 | Full score inside the preferred range, decaying to 0 the further the destination's average temperature for that month is outside it |
| Flight distance | 20 | Full score within the selected flight-time bracket (≤3h / ≤5h), decaying for destinations further away; "anywhere" always gets a flat mid-score |
| Trip length fit | 10 | Full score if requested days fall within the destination's supported range, small partial credit just outside it |

Destinations scoring 0 are excluded entirely; the rest are capped at 100 and
sorted descending. This logic is covered by a dedicated test suite
(`tests/Feature/Services/DestinationRecommendationServiceTest.php`) that
verifies each scoring component in isolation.

### External API integration

- **Frankfurter** is called on demand (destination detail & compare pages)
  to convert EUR to the destination country's currency. Requests are wrapped
  in a fallback that retries without TLS verification on local environments
  with broken CA bundles (`cURL error 60`), and failures degrade gracefully
  (no rate shown) instead of breaking the page.
- **GeoNames** flag images are referenced directly by ISO country code —
  no API key or request needed, just a predictable static image URL.

### Visit tracking

The `TrackVisit` middleware logs one row per GET request (ip hash + time
bucket), which powers the statistics dashboard's "unique visits" and
"traffic by time of day" figures without ever persisting raw IP addresses.

## Local Setup

Prerequisites: PHP 8.3+, Composer, Node.js + npm, MySQL.

```bash
git clone https://github.com/ValeriiTheProger/vacation_choice.git
cd vacation_choice/vacation-choice

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure your database in `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`),
then:

```bash
php artisan migrate
php artisan db:seed   # optional, loads sample countries/destinations

npm run build         # or `npm run dev` for hot-reloading during development
php artisan serve
```

The app is served under the `/vacation-choice` prefix, e.g.
`http://127.0.0.1:8000/vacation-choice/`.

## Running Tests

```bash
php artisan test
```

Tests run against an in-memory SQLite database (configured in `phpunit.xml`),
so no database setup is required to run the suite. The PHP `pdo_sqlite`
extension must be enabled.

## Production Deployment

Server prerequisites:

- Nginx
- MySQL
- PHP 8.3+ with PHP-FPM
- Required PHP extensions: `pdo_mysql`, `mbstring`, `xml`, `curl`, `bcmath`, `intl`, `zip`
- Composer
- Node.js + npm (to build frontend assets)

**1. Upload the project**, e.g. to `/var/www/vacation-choice`, owned by the
web server user (e.g. `www-data`).

**2. Install dependencies:**

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

**3. Configure the environment:**

```bash
cp .env.example .env
```

Set at minimum:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.tld
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=db_user
DB_PASSWORD=db_password
```

**4. Initialize the application:**

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force   # optional, only for reference data
```

**5. File permissions** — the web server user needs write access to:

```bash
chown -R www-data:www-data /var/www/vacation-choice
chmod -R 775 /var/www/vacation-choice/storage /var/www/vacation-choice/bootstrap/cache
```

**6. Nginx server block** (document root must point at `public/`):

```nginx
server {
    listen 80;
    server_name yourdomain.tld;
    root /var/www/vacation-choice/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

**7. Cache config for production:**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**8. Reload services:**

```bash
nginx -t
systemctl reload nginx
systemctl restart php8.3-fpm
```

**9. (Optional) Scheduler cron**, if the app relies on `php artisan schedule:run`:

```
* * * * * cd /var/www/vacation-choice && php artisan schedule:run >> /dev/null 2>&1
```

## Data Model

| Table | Purpose | Relationships |
|---|---|---|
| `countries` | Countries destinations belong to | 1:N `destinations` |
| `destinations` | Vacation destinations | N:1 `countries`, M:N `vacation_types` (via `destination_vacation_type`), 1:N `monthly_weather`, 1:N `destination_search_logs` |
| `vacation_types` | Vacation type catalog (beach, nature, city, ...) | M:N `destinations` |
| `destination_vacation_type` | Pivot: destination ↔ vacation type | unique (`destination_id`, `vacation_type_id`) |
| `monthly_weather` | Average monthly weather per destination | belongs to `destinations`; unique (`destination_id`, `month`) |
| `visit_logs` | Page visit log, used for statistics | standalone |
| `search_logs` | Recorded search filter parameters | 1:N `destination_search_logs` |
| `destination_search_logs` | Which destinations a search recommended, and their score | belongs to `search_logs` and `destinations`; unique (`search_log_id`, `destination_id`) |
| `sessions` | Laravel's standard session storage | — |
