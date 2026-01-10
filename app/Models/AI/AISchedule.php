<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Model;

class AISchedule extends Model
{
    protected $table = 'ai_schedules';

    protected $fillable = [
        'type',
        'params',
        'status',
        'run_at',
        'completed_at',
        'output',
        'error_message',
    ];

    protected $casts = [
        'params' => 'array',
        'output' => 'array',
        'run_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
