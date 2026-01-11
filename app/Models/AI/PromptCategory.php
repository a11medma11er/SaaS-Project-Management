<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptCategory extends Model
{
    use HasFactory;

    protected $table = 'prompt_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get prompts in this category
     */
    public function prompts()
    {
        return $this->hasMany(AIPrompt::class, 'category_id');
    }

    /**
     * Get active prompts count
     */
    public function getActivePromptsCountAttribute()
    {
        return $this->prompts()->where('is_active', true)->count();
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
