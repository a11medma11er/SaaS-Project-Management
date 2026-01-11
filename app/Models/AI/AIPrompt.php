<?php

namespace App\Models\AI;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIPrompt extends Model
{
    use HasFactory;

    protected $table = 'ai_prompts';

    protected $fillable = [
        'name',
        'type',
        'category_id',
        'template',
        'version',
        'variables',
        'description',
        'is_active',
        'usage_count',
        'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(PromptCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(
            PromptTag::class,
            'prompt_tag_pivot',
            'ai_prompt_id',
            'prompt_tag_id'
        )->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeWithTags($query, array $tagIds)
    {
        return $query->whereHas('tags', function ($q) use ($tagIds) {
            $q->whereIn('prompt_tags.id', $tagIds);
        });
    }

    // Helper Methods
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
