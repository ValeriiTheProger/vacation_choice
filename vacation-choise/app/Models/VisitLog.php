<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_hash',
        'user_agent',
        'time_period',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];
}
