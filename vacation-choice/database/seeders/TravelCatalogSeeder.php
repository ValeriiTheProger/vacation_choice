<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TravelCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $vacationTypes = [
            ['name' => 'More a plaz', 'code' => 'beach'],
            ['name' => 'Hory a priroda', 'code' => 'nature'],
            ['name' => 'Historicke mesta', 'code' => 'history'],
            ['name' => 'Mestsky vylet', 'code' => 'city'],
            ['name' => 'Aktivity a dobrodruzstvo', 'code' => 'adventure'],
        ];

        $countriesData = [
            'HR' => ['name' => 'Croatia', 'name_sk' => 'Chorvatsko', 'iso_code' => 'HR', 'capital' => 'Zagreb', 'currency_code' => 'EUR'],
            'GR' => ['name' => 'Greece', 'name_sk' => 'Grecko', 'iso_code' => 'GR', 'capital' => 'Athens', 'currency_code' => 'EUR'],
            'IT' => ['name' => 'Italy', 'name_sk' => 'Taliansko', 'iso_code' => 'IT', 'capital' => 'Rome', 'currency_code' => 'EUR'],
            'ES' => ['name' => 'Spain', 'name_sk' => 'Spanielsko', 'iso_code' => 'ES', 'capital' => 'Madrid', 'currency_code' => 'EUR'],
            'AT' => ['name' => 'Austria', 'name_sk' => 'Rakusko', 'iso_code' => 'AT', 'capital' => 'Vienna', 'currency_code' => 'EUR'],
            'CZ' => ['name' => 'Czech Republic', 'name_sk' => 'Cesko', 'iso_code' => 'CZ', 'capital' => 'Prague', 'currency_code' => 'CZK'],
            'FR' => ['name' => 'France', 'name_sk' => 'Francuzsko', 'iso_code' => 'FR', 'capital' => 'Paris', 'currency_code' => 'EUR'],
            'PT' => ['name' => 'Portugal', 'name_sk' => 'Portugalsko', 'iso_code' => 'PT', 'capital' => 'Lisbon', 'currency_code' => 'EUR'],
            'NO' => ['name' => 'Norway', 'name_sk' => 'Norsko', 'iso_code' => 'NO', 'capital' => 'Oslo', 'currency_code' => 'NOK'],
            'CH' => ['name' => 'Switzerland', 'name_sk' => 'Svajciarsko', 'iso_code' => 'CH', 'capital' => 'Bern', 'currency_code' => 'CHF'],
        ];

        $destinations = [
            ['country_code' => 'HR', 'name' => 'Split', 'short_description' => 'Primorske mesto s krasnymi plazami a historickym centrom.', 'latitude' => 43.5081, 'longitude' => 16.4402, 'flight_minutes_from_vienna' => 70, 'min_days' => 3, 'max_days' => 10, 'image_path' => 'images/destinations/Split-Crotia.png', 'types' => ['beach', 'history', 'city']],
            ['country_code' => 'HR', 'name' => 'Dubrovnik', 'short_description' => 'Historicke mesto pri mori s vynimocnou atmosferou.', 'latitude' => 42.6507, 'longitude' => 18.0944, 'flight_minutes_from_vienna' => 80, 'min_days' => 3, 'max_days' => 10, 'image_path' => 'images/destinations/Dubrovnik-Crotia.png', 'types' => ['beach', 'history', 'city']],
            ['country_code' => 'GR', 'name' => 'Ateny', 'short_description' => 'Mesto plne historie, pamiatok a vyborneho jedla.', 'latitude' => 37.9838, 'longitude' => 23.7275, 'flight_minutes_from_vienna' => 135, 'min_days' => 3, 'max_days' => 7, 'image_path' => 'images/destinations/Athens-Greece.png', 'types' => ['history', 'city']],
            ['country_code' => 'IT', 'name' => 'Rim', 'short_description' => 'Historicke hlavne mesto s kulturou, gastronomiou a pamiatkami.', 'latitude' => 41.9028, 'longitude' => 12.4964, 'flight_minutes_from_vienna' => 100, 'min_days' => 2, 'max_days' => 7, 'image_path' => 'images/destinations/Rome-Italy.png', 'types' => ['history', 'city']],
            ['country_code' => 'ES', 'name' => 'Barcelona', 'short_description' => 'Mestska dovolenka pri mori s architekturou, plazami a gastronomiou.', 'latitude' => 41.3874, 'longitude' => 2.1686, 'flight_minutes_from_vienna' => 145, 'min_days' => 3, 'max_days' => 10, 'image_path' => 'images/destinations/Barcelona-Spain.png', 'types' => ['beach', 'history', 'city']],
            ['country_code' => 'AT', 'name' => 'Innsbruck', 'short_description' => 'Alpske mesto vhodne na hory, prirodu a aktivny oddych.', 'latitude' => 47.2692, 'longitude' => 11.4041, 'flight_minutes_from_vienna' => 60, 'min_days' => 2, 'max_days' => 7, 'image_path' => 'images/destinations/Innsbruck-Austria.png', 'types' => ['nature', 'city', 'adventure']],
            ['country_code' => 'CZ', 'name' => 'Praha', 'short_description' => 'Historicke mesto vhodne na vikendovy mestsky vylet.', 'latitude' => 50.0755, 'longitude' => 14.4378, 'flight_minutes_from_vienna' => 55, 'min_days' => 2, 'max_days' => 5, 'image_path' => 'images/destinations/Prague-Czech.png', 'types' => ['history', 'city']],
            ['country_code' => 'FR', 'name' => 'Nice', 'short_description' => 'Slnecne mesto na Francuzskej riviere s plazami a mestskou atmosferou.', 'latitude' => 43.7102, 'longitude' => 7.2620, 'flight_minutes_from_vienna' => 110, 'min_days' => 3, 'max_days' => 10, 'image_path' => 'images/destinations/Nice-France.png', 'types' => ['beach', 'city']],
            ['country_code' => 'PT', 'name' => 'Lisabon', 'short_description' => 'Farebne hlavne mesto Portugalska s oceanom, historiou a vyhladmi.', 'latitude' => 38.7223, 'longitude' => -9.1393, 'flight_minutes_from_vienna' => 210, 'min_days' => 3, 'max_days' => 10, 'image_path' => 'images/destinations/Lisabon-portugal.png', 'types' => ['history', 'city', 'beach']],
            ['country_code' => 'NO', 'name' => 'Bergen', 'short_description' => 'Severske mesto obklopene fjordmi, horami a prirodou.', 'latitude' => 60.3913, 'longitude' => 5.3221, 'flight_minutes_from_vienna' => 170, 'min_days' => 3, 'max_days' => 8, 'image_path' => 'images/destinations/Bergen-Norway.png', 'types' => ['nature', 'city', 'adventure']],
            ['country_code' => 'CH', 'name' => 'Interlaken', 'short_description' => 'Horska destinacia medzi jazerami, idealna na turistiku a dobrodruzstvo.', 'latitude' => 46.6863, 'longitude' => 7.8632, 'flight_minutes_from_vienna' => 90, 'min_days' => 3, 'max_days' => 8, 'image_path' => 'images/destinations/Interlaken-Switzerland.png', 'types' => ['nature', 'adventure']],
            ['country_code' => 'ES', 'name' => 'Madrid', 'short_description' => 'Energicke hlavne mesto Spanielska s muzeami, parkami a nocnym zivotom.', 'latitude' => 40.4168, 'longitude' => -3.7038, 'flight_minutes_from_vienna' => 185, 'min_days' => 3, 'max_days' => 7, 'image_path' => 'images/destinations/Madrid-Spain.png', 'types' => ['city', 'history']],
            ['country_code' => 'ES', 'name' => 'Valencia', 'short_description' => 'Moderne prímorske mesto s plazami, architekturou a skvelou kuchynou.', 'latitude' => 39.4699, 'longitude' => -0.3763, 'flight_minutes_from_vienna' => 165, 'min_days' => 3, 'max_days' => 9, 'image_path' => 'images/destinations/Valencia-Spain.png', 'types' => ['beach', 'city']],
            ['country_code' => 'IT', 'name' => 'Neapol', 'short_description' => 'Juzanske mesto pri mori, brana k Amalfi pobreziu a Pompejam.', 'latitude' => 40.8518, 'longitude' => 14.2681, 'flight_minutes_from_vienna' => 105, 'min_days' => 3, 'max_days' => 8, 'image_path' => 'images/destinations/Naples-Italy.png', 'types' => ['history', 'city', 'beach']],
            ['country_code' => 'IT', 'name' => 'Milan', 'short_description' => 'Stylove mesto severneho Talianska so silnou kulturou a gastronomiou.', 'latitude' => 45.4642, 'longitude' => 9.1900, 'flight_minutes_from_vienna' => 85, 'min_days' => 2, 'max_days' => 6, 'image_path' => 'images/destinations/Milan-Italy.png', 'types' => ['city', 'history']],
            ['country_code' => 'FR', 'name' => 'Lyon', 'short_description' => 'Francuzske mesto medzi riekami, zname svojou kuchynou a historiou.', 'latitude' => 45.7640, 'longitude' => 4.8357, 'flight_minutes_from_vienna' => 95, 'min_days' => 2, 'max_days' => 6, 'image_path' => 'images/destinations/Lyon-France.png', 'types' => ['city', 'history']],
            ['country_code' => 'PT', 'name' => 'Porto', 'short_description' => 'Atmosfericke mesto pri rieke Douro so slavnym vinom a pobrezim Atlantiku.', 'latitude' => 41.1579, 'longitude' => -8.6291, 'flight_minutes_from_vienna' => 205, 'min_days' => 3, 'max_days' => 8, 'image_path' => 'images/destinations/Porto-Portugal.png', 'types' => ['city', 'history', 'beach']],
            ['country_code' => 'AT', 'name' => 'Salzburg', 'short_description' => 'Elegantne alpske mesto idealne na kulturu, prirodu a kratke vylety.', 'latitude' => 47.8095, 'longitude' => 13.0550, 'flight_minutes_from_vienna' => 50, 'min_days' => 2, 'max_days' => 5, 'image_path' => 'images/destinations/Salzburg-Austria.png', 'types' => ['city', 'nature']],
            ['country_code' => 'CZ', 'name' => 'Brno', 'short_description' => 'Studentske mesto s kavarnami, modernou architekturou a pohodovou atmosferou.', 'latitude' => 49.1951, 'longitude' => 16.6068, 'flight_minutes_from_vienna' => 40, 'min_days' => 2, 'max_days' => 4, 'image_path' => 'images/destinations/Brno-Czech.png', 'types' => ['city']],
            ['country_code' => 'GR', 'name' => 'Solun', 'short_description' => 'Pristavne mesto pri Egejskom mori, vhodne na mestsku aj letnu dovolenku.', 'latitude' => 40.6401, 'longitude' => 22.9444, 'flight_minutes_from_vienna' => 120, 'min_days' => 3, 'max_days' => 8, 'image_path' => 'images/destinations/Thessaloniki-Greece.png', 'types' => ['city', 'beach', 'history']],
            ['country_code' => 'CH', 'name' => 'Zermatt', 'short_description' => 'Horska perla pod Matterhornom pre turistiku, lyzovanie a aktivny oddych.', 'latitude' => 46.0207, 'longitude' => 7.7491, 'flight_minutes_from_vienna' => 100, 'min_days' => 3, 'max_days' => 7, 'image_path' => 'images/destinations/Zermatt-Switzerland.png', 'types' => ['nature', 'adventure']],
        ];

        $weatherByCity = [
            'Split' => [1 => [8,5,12],2 => [9,6,13],3 => [12,8,16],4 => [16,11,20],5 => [21,15,25],6 => [25,19,29],7 => [28,22,32],8 => [28,22,32],9 => [24,18,28],10 => [19,14,23],11 => [14,10,18],12 => [10,6,14]],
            'Dubrovnik' => [1 => [9,6,13],2 => [10,7,14],3 => [13,9,17],4 => [16,12,20],5 => [21,16,25],6 => [25,20,29],7 => [28,23,32],8 => [28,23,32],9 => [24,19,28],10 => [20,15,24],11 => [15,11,19],12 => [11,8,15]],
            'Ateny' => [1 => [10,7,14],2 => [11,7,15],3 => [14,9,18],4 => [18,12,23],5 => [23,17,28],6 => [28,22,33],7 => [31,24,36],8 => [31,24,36],9 => [27,21,32],10 => [22,16,27],11 => [17,12,21],12 => [12,8,16]],
            'Rim' => [1 => [8,4,13],2 => [9,5,14],3 => [12,7,17],4 => [15,10,20],5 => [20,14,25],6 => [24,18,29],7 => [27,21,32],8 => [27,21,32],9 => [23,17,28],10 => [18,13,23],11 => [13,8,18],12 => [9,5,14]],
            'Barcelona' => [1 => [10,6,14],2 => [11,7,15],3 => [13,9,17],4 => [15,11,19],5 => [19,15,23],6 => [23,19,27],7 => [26,22,30],8 => [26,22,30],9 => [23,19,27],10 => [19,15,23],11 => [14,10,18],12 => [11,7,15]],
            'Innsbruck' => [1 => [-1,-5,4],2 => [1,-4,6],3 => [6,0,12],4 => [10,4,16],5 => [15,8,21],6 => [19,12,25],7 => [21,14,27],8 => [21,14,27],9 => [17,10,23],10 => [11,5,17],11 => [5,0,10],12 => [0,-4,5]],
            'Praha' => [1 => [0,-3,3],2 => [2,-2,5],3 => [6,1,11],4 => [11,5,17],5 => [16,9,22],6 => [19,12,25],7 => [21,14,27],8 => [21,14,26],9 => [16,10,22],10 => [10,5,15],11 => [5,1,9],12 => [1,-2,4]],
            'Nice' => [1 => [9,5,13],2 => [10,6,14],3 => [12,8,16],4 => [15,11,19],5 => [19,15,23],6 => [23,19,27],7 => [26,22,30],8 => [26,22,30],9 => [23,19,27],10 => [18,14,22],11 => [13,9,17],12 => [10,6,14]],
            'Lisabon' => [1 => [12,8,16],2 => [13,9,17],3 => [15,10,19],4 => [16,11,20],5 => [19,13,23],6 => [22,16,27],7 => [24,18,29],8 => [24,18,29],9 => [23,17,28],10 => [19,14,24],11 => [15,11,19],12 => [13,9,17]],
            'Bergen' => [1 => [3,0,5],2 => [3,0,6],3 => [5,1,8],4 => [8,4,12],5 => [12,7,16],6 => [15,10,19],7 => [17,12,21],8 => [16,12,20],9 => [13,9,17],10 => [9,6,13],11 => [6,3,9],12 => [4,1,7]],
            'Interlaken' => [1 => [0,-4,4],2 => [2,-3,6],3 => [6,0,11],4 => [10,4,15],5 => [15,8,20],6 => [18,12,24],7 => [21,14,27],8 => [20,14,26],9 => [16,10,22],10 => [11,5,16],11 => [5,0,10],12 => [1,-3,5]],
            'Madrid' => [1 => [7,2,12],2 => [9,3,15],3 => [12,5,18],4 => [15,7,21],5 => [20,11,26],6 => [25,16,32],7 => [29,19,36],8 => [29,19,35],9 => [24,15,30],10 => [18,10,24],11 => [12,6,17],12 => [8,3,13]],
            'Valencia' => [1 => [11,6,16],2 => [12,7,17],3 => [14,9,19],4 => [16,11,21],5 => [20,15,25],6 => [24,19,29],7 => [27,22,32],8 => [27,22,32],9 => [24,19,29],10 => [20,15,25],11 => [15,10,20],12 => [12,7,17]],
            'Neapol' => [1 => [9,5,13],2 => [10,5,14],3 => [13,7,17],4 => [16,10,21],5 => [21,14,26],6 => [25,18,30],7 => [28,21,33],8 => [28,21,33],9 => [24,18,29],10 => [19,13,24],11 => [14,9,19],12 => [10,6,14]],
            'Milan' => [1 => [4,0,8],2 => [7,1,12],3 => [11,5,16],4 => [15,8,20],5 => [20,12,25],6 => [24,16,29],7 => [27,19,32],8 => [27,19,31],9 => [22,15,27],10 => [16,10,21],11 => [9,4,14],12 => [5,1,9]],
            'Lyon' => [1 => [5,1,9],2 => [7,2,12],3 => [11,4,16],4 => [14,7,19],5 => [18,10,23],6 => [22,14,27],7 => [25,17,30],8 => [24,16,29],9 => [20,13,25],10 => [15,9,20],11 => [9,4,13],12 => [6,2,10]],
            'Porto' => [1 => [10,6,14],2 => [11,7,15],3 => [13,8,17],4 => [14,9,18],5 => [17,11,22],6 => [20,14,25],7 => [22,16,27],8 => [22,16,27],9 => [21,15,26],10 => [18,13,23],11 => [14,10,18],12 => [11,7,15]],
            'Salzburg' => [1 => [-1,-5,3],2 => [1,-4,5],3 => [6,0,11],4 => [11,4,16],5 => [16,8,21],6 => [20,12,25],7 => [22,14,27],8 => [22,14,27],9 => [17,10,22],10 => [11,5,16],11 => [5,0,9],12 => [1,-3,4]],
            'Brno' => [1 => [0,-3,3],2 => [2,-2,5],3 => [7,1,12],4 => [12,5,17],5 => [17,10,22],6 => [20,13,25],7 => [22,15,28],8 => [22,14,27],9 => [17,11,23],10 => [11,6,16],11 => [5,1,9],12 => [1,-2,4]],
            'Solun' => [1 => [7,3,12],2 => [9,4,14],3 => [12,6,17],4 => [16,10,22],5 => [21,14,27],6 => [26,19,32],7 => [29,22,35],8 => [29,22,35],9 => [24,18,30],10 => [18,12,24],11 => [12,7,17],12 => [8,4,13]],
            'Zermatt' => [1 => [-3,-8,1],2 => [-1,-7,3],3 => [2,-4,7],4 => [6,0,11],5 => [11,4,16],6 => [15,8,20],7 => [18,10,23],8 => [17,10,22],9 => [13,7,18],10 => [8,2,13],11 => [2,-3,7],12 => [-2,-7,2]],
        ];

        $rainyDaysByMonth = [1 => 11,2 => 10,3 => 10,4 => 9,5 => 8,6 => 7,7 => 6,8 => 6,9 => 8,10 => 10,11 => 11,12 => 11];

        DB::transaction(function () use ($vacationTypes, $countriesData, $destinations, $weatherByCity, $rainyDaysByMonth) {
            DB::table('destination_vacation_type')->delete();
            DB::table('monthly_weather')->delete();
            DB::table('destinations')->delete();
            DB::table('vacation_types')->delete();
            DB::table('countries')->delete();

            $now = now();

            foreach ($vacationTypes as $type) {
                DB::table('vacation_types')->insert([
                    'name' => $type['name'],
                    'code' => $type['code'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($countriesData as $country) {
                DB::table('countries')->insert([
                    'name' => $country['name'],
                    'name_sk' => $country['name_sk'],
                    'iso_code' => $country['iso_code'],
                    'capital' => $country['capital'],
                    'currency_code' => $country['currency_code'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $countryIds = DB::table('countries')->pluck('id', 'iso_code');
            $vacationTypeIds = DB::table('vacation_types')->pluck('id', 'code');

            foreach ($destinations as $destination) {
                $destinationId = DB::table('destinations')->insertGetId([
                    'country_id' => $countryIds[$destination['country_code']],
                    'name' => $destination['name'],
                    'slug' => Str::slug($destination['name']),
                    'short_description' => $destination['short_description'],
                    'latitude' => $destination['latitude'],
                    'longitude' => $destination['longitude'],
                    'flight_minutes_from_vienna' => $destination['flight_minutes_from_vienna'],
                    'min_days' => $destination['min_days'],
                    'max_days' => $destination['max_days'],
                    'image_path' => $destination['image_path'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($destination['types'] as $typeCode) {
                    DB::table('destination_vacation_type')->insert([
                        'destination_id' => $destinationId,
                        'vacation_type_id' => $vacationTypeIds[$typeCode],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                foreach ($weatherByCity[$destination['name']] as $month => $temps) {
                    DB::table('monthly_weather')->insert([
                        'destination_id' => $destinationId,
                        'month' => $month,
                        'avg_temp' => $temps[0],
                        'min_temp' => $temps[1],
                        'max_temp' => $temps[2],
                        'rainy_days' => $rainyDaysByMonth[$month],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        });
    }
}
