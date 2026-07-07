<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'slug',
        'short_description',
        'latitude',
        'longitude',
        'flight_minutes_from_vienna',
        'min_days',
        'max_days',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function vacationTypes(): BelongsToMany
    {
        return $this->belongsToMany(VacationType::class, 'destination_vacation_type')->withTimestamps();
    }

    public function monthlyWeather(): HasMany
    {
        return $this->hasMany(MonthlyWeather::class);
    }

    public function searchMatches(): HasMany
    {
        return $this->hasMany(DestinationSearchLog::class);
    }
}
