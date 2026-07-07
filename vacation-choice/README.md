# Technicka sprava - Vacation Choice

Tento dokument popisuje technicke aspekty riesenia: externe API, postup nasadenia a datovy model databazy.

Zoznam pouzitych externych API a ich ucel

1) Frankfurter API (https://api.frankfurter.dev/v1/latest)
Ucel: Ziskanie aktualneho kurzu meny pre vybranu destinaciu. Aplikacia prepocitava kurz z EUR na menu krajiny destinacie a zobrazuje ho v detaile a porovnani destinacii.

2) GeoNames - vlajky krajin (https://www.geonames.org/flags/)
Ucel: Zobrazenie vlajky krajiny destinacie podla ISO kodu krajiny. Pouziva sa staticky obrazok vlajky vo formate GIF.

Postup nasadenia riesenia krok za krokom

Predpoklady servera
- Nginx
- MySQL
- PHP 8.3+
- PHP-FPM
- Composer
- Node.js + npm (potrebne na zostavenie frontend assetov)

Krok 1: Priprava servera
- Nainstalujte PHP 8.3, PHP-FPM a Composer.
- Povinne PHP rozsirenia: pdo_mysql, mbstring, xml, curl, bcmath, intl, zip.
- Nainstalujte Node.js a npm.

Krok 2: Nahratie projektu
- Nahrajte projekt do adresara, napr. /var/www/vacation-choice.
- Nastavte vlastnika suborov na uzivatela web servera (napr. www-data).

Krok 3: Instalacia zavislosti
V koreni projektu spustite:
- composer install --no-dev --optimize-autoloader
- npm ci
- npm run build

Krok 4: Konfiguracia prostredia
- Skopirujte .env.example na .env.
- V .env nastavte hlavne tieto hodnoty:
  APP_ENV=production
  APP_DEBUG=false
  APP_URL=https://vasadomena.tld
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=nazov_databazy
  DB_USERNAME=db_user
  DB_PASSWORD=db_heslo

Krok 5: Inicializacia aplikacie
- php artisan key:generate
- php artisan migrate --force
- php artisan db:seed --force (iba ak chcete naplnit referencne data)

Krok 6: Prava na zapis
Aplikacia musi mat pravo zapisu do:
- storage/
- bootstrap/cache/

Priklad:
- chown -R www-data:www-data /var/www/vacation-choice
- chmod -R 775 /var/www/vacation-choice/storage /var/www/vacation-choice/bootstrap/cache

Krok 7: Konfiguracia Nginx
Dolezite: document root musi smerovat na public/.

Priklad server bloku:
server {
    listen 80;
    server_name vasadomena.tld;
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

Krok 8: Optimalizacia pre produkciu
- php artisan config:cache
- php artisan route:cache
- php artisan view:cache

Krok 9: Restart sluzieb
- nginx -t
- systemctl reload nginx
- systemctl restart php8.3-fpm

Krok 10: (Volitelne) Cron pre scheduler
Ak aplikacia pouziva scheduler, pridajte cron:
* * * * * cd /var/www/vacation-choice && php artisan schedule:run >> /dev/null 2>&1

Popis datoveho modelu databazy

1) countries
Ucel: Zoznam krajin pre destinacie.
Hlavne stlpce:
- id (PK)
- name (nazov krajiny)
- name_sk (nazov krajiny v slovencine)
- iso_code (3-znakovy kod, unikatny)
- capital (hlavne mesto)
- currency_code (3-znakovy kod meny)
Vazby:
- 1:N na destinations (jedna krajina moze mat viac destinacii)

2) destinations
Ucel: Zoznam dovolenkovych destinacii.
Hlavne stlpce:
- id (PK)
- country_id (FK -> countries.id)
- name, slug (slug je unikatny)
- short_description
- latitude, longitude
- flight_minutes_from_vienna
- min_days, max_days
- image_path (nullable)
- is_active
Vazby:
- N:1 na countries
- M:N na vacation_types cez pivot destination_vacation_type
- 1:N na monthly_weather
- 1:N na destination_search_logs

3) vacation_types
Ucel: Ciselnik typov dovoleniek (napr. beach, nature, city, ...).
Hlavne stlpce:
- id (PK)
- name
- code (unikatny)
Vazby:
- M:N na destinations cez pivot destination_vacation_type

4) destination_vacation_type
Ucel: Pivot tabulka pre vztah destinacia <-> typ dovolenky.
Hlavne stlpce:
- id (PK)
- destination_id (FK -> destinations.id)
- vacation_type_id (FK -> vacation_types.id)
Obmedzenia:
- unikatna dvojica (destination_id, vacation_type_id)

5) monthly_weather
Ucel: Priemerne mesacne pocasie pre destinaciu.
Hlavne stlpce:
- id (PK)
- destination_id (FK -> destinations.id)
- month (1-12)
- avg_temp, min_temp, max_temp
- rainy_days
Obmedzenia:
- unikatna dvojica (destination_id, month)
Poznamka:
- Migracia, ktora by mala tabulku mazat, je v projekte zamerne prazdna. Tabulka sa teda bezne nemaze.

6) visit_logs
Ucel: Log navstev pre statistiky.
Hlavne stlpce:
- id (PK)
- ip_hash
- user_agent (nullable)
- time_period
- visited_at
Vazby:
- bez FK, samostatna logovacia tabulka.

7) search_logs
Ucel: Zaznam parametrov vyhladavania od pouzivatelov.
Hlavne stlpce:
- id (PK)
- month
- days
- temperature_preference
- distance_preference
- vacation_types (JSON, nullable)
- searched_at
Vazby:
- 1:N na destination_search_logs

8) destination_search_logs
Ucel: Vysledky konkretneho vyhladavania (ktore destinacie boli odporucene a s akym skore).
Hlavne stlpce:
- id (PK)
- search_log_id (FK -> search_logs.id)
- destination_id (FK -> destinations.id)
- score
Obmedzenia:
- unikatna dvojica (search_log_id, destination_id)

9) sessions
Ucel: Laravel sessions (standardny mechanizmus frameworku).
Hlavne stlpce:
- id (PK, string)
- user_id (nullable)
- ip_address, user_agent
- payload
- last_activity

Zhrnutie vztahov
- countries 1:N destinations
- destinations M:N vacation_types (cez destination_vacation_type)
- destinations 1:N monthly_weather
- search_logs 1:N destination_search_logs
- destinations 1:N destination_search_logs
