<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyWeather extends Model
{
    use HasFactory;

    protected $table = 'monthly_weather';

    protected $fillable = [
        'destination_id',
        'month',
        'avg_temp',
        'min_temp',
        'max_temp',
        'rainy_days',
    ];

    protected $casts = [
        'month' => 'integer',
        'avg_temp' => 'float',
        'min_temp' => 'float',
        'max_temp' => 'float',
        'rainy_days' => 'integer',
    ];

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
