<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptTag extends Model
{
    use HasFactory;

    protected $table = 'prompt_tags';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'usage_count',
    ];

    protected $casts = [
        'usage_count' => 'integer',
    ];

    /**
     * Get prompts with this tag
     */
    public function prompts()
    {
        return $this->belongsToMany(
            AIPrompt::class,
            'prompt_tag_pivot',
            'prompt_tag_id',
            'ai_prompt_id'
        )->withTimestamps();
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Scope for popular tags
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * Scope for alphabetically ordered tags
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name');
    }
}
