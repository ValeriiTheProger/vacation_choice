<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestinationSearchLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_log_id',
        'destination_id',
        'score',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    public function searchLog(): BelongsTo
    {
        return $this->belongsTo(SearchLog::class);
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
