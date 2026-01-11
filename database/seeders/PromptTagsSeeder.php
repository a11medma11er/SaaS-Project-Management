<?php

namespace Database\Seeders;

use App\Models\AI\PromptTag;
use Illuminate\Database\Seeder;

class PromptTagsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'automation', 'slug' => 'automation', 'color' => '#10b981'],
            ['name' => 'planning', 'slug' => 'planning', 'color' => '#3b82f6'],
            ['name' => 'analysis', 'slug' => 'analysis', 'color' => '#8b5cf6'],
            ['name' => 'estimation', 'slug' => 'estimation', 'color' => '#f59e0b'],
            ['name' => 'reporting', 'slug' => 'reporting', 'color' => '#06b6d4'],
            ['name' => 'brainstorming', 'slug' => 'brainstorming', 'color' => '#ec4899'],
            ['name' => 'review', 'slug' => 'review', 'color' => '#f97316'],
            ['name' => 'optimization', 'slug' => 'optimization', 'color' => '#14b8a6'],
            ['name' => 'documentation', 'slug' => 'documentation', 'color' => '#6366f1'],
            ['name' => 'decision-making', 'slug' => 'decision-making', 'color' => '#a855f7'],
            ['name' => 'risk-management', 'slug' => 'risk-management', 'color' => '#ef4444'],
            ['name' => 'time-management', 'slug' => 'time-management', 'color' => '#84cc16'],
            ['name' => 'collaboration', 'slug' => 'collaboration', 'color' => '#22c55e'],
            ['name' => 'technical', 'slug' => 'technical', 'color' => '#64748b'],
            ['name' => 'business', 'slug' => 'business', 'color' => '#0ea5e9'],
        ];

        foreach ($tags as $tag) {
            PromptTag::updateOrCreate(
                ['slug' => $tag['slug']],
                $tag
            );
        }

        $this->command->info('Prompt tags seeded successfully!');
    }
}
