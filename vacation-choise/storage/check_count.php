<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$count = Illuminate\Support\Facades\DB::table('monthly_weather')->count();
file_put_contents('php://stdout', "monthly_weather rows: {$count}" . PHP_EOL);
