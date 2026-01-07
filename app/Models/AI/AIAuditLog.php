<?php

namespace App\Models\AI;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'decision_id',
        'user_id',
        'action',
        'context',
        'reason',
    ];

    protected $casts = [
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = ['created_at']; // Only created_at, no updated_at

    // Relationships
    public function decision()
    {
        return $this->belongsTo(AIDecision::class, 'decision_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
