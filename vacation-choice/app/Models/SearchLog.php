<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'days',
        'temperature_preference',
        'distance_preference',
        'vacation_types',
        'searched_at',
    ];

    protected $casts = [
        'month' => 'integer',
        'days' => 'integer',
        'vacation_types' => 'array',
        'searched_at' => 'datetime',
    ];

    public function destinationMatches(): HasMany
    {
        return $this->hasMany(DestinationSearchLog::class);
    }
}
