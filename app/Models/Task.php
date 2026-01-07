<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_number',
        'title',
        'description',
        'project_id',
        'client_name',
        'due_date',
        'status',
        'priority',
        'created_by',
    ];


    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->whereNull('parent_id');
    }

    public function allComments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function tags()
    {
        return $this->hasMany(TaskTag::class);
    }

    public function subTasks()
    {
        return $this->hasMany(TaskSubTask::class);
    }

    public function timeEntries()
    {
        return $this->hasMany(TaskTimeEntry::class);
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('task_number', 'like', "%{$term}%")
              ->orWhere('client_name', 'like', "%{$term}%");
        });
    }


    // Auto-generate task number
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($task) {
            if (empty($task->task_number)) {
                // Use max ID to avoid race condition
                $maxId = \DB::table('tasks')->max('id') ?? 0;
                $nextNumber = $maxId + 1;
                $task->task_number = '#VLZ' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

}
