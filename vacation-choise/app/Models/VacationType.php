<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VacationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
    ];

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'destination_vacation_type')->withTimestamps();
    }
}
