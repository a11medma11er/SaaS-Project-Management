<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use \Spatie\Permission\Traits\HasRoles;
    
    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Get tasks created by this user
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }
    
    /**
     * Get tasks assigned to this user
     */
    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')
            ->withPivot('role')
            ->withTimestamps();
    }
    
    /**
     * Get projects created by this user
     */
    public function createdProjects()
    {
        return $this->hasMany(Project::class, 'created_by');
    }
    
    /**
     * Get projects where user is team lead
     */
    public function ledProjects()
    {
        return $this->hasMany(Project::class, 'team_lead_id');
    }
    
    /**
     * Get projects this user is a member of
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }
}
