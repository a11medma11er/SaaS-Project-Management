<?php

namespace Database\Seeders;

use App\Models\AI\PromptCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PromptCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Task Management',
                'slug' => 'task-management',
                'description' => 'Prompts for analyzing, breaking down, and managing tasks',
                'icon' => 'ri-task-line',
                'color' => '#3b82f6', // blue
                'order' => 1,
            ],
            [
                'name' => 'Project Analysis',
                'slug' => 'project-analysis',
                'description' => 'Prompts for project evaluation, feasibility studies, and planning',
                'icon' => 'ri-line-chart-line',
                'color' => '#8b5cf6', // purple
                'order' => 2,
            ],
            [
                'name' => 'AI Automation',
                'slug' => 'ai-automation',
                'description' => 'Prompts for automated workflows and decision-making',
                'icon' => 'ri-robot-line',
                'color' => '#10b981', // green
                'order' => 3,
            ],
            [
                'name' => 'Content Generation',
                'slug' => 'content-generation',
                'description' => 'Prompts for generating documentation, emails, and reports',
                'icon' => 'ri-file-text-line',
                'color' => '#f59e0b', // amber
                'order' => 4,
            ],
            [
                'name' => 'Data Analysis',
                'slug' => 'data-analysis',
                'description' => 'Prompts for analyzing data, metrics, and performance',
                'icon' => 'ri-bar-chart-box-line',
                'color' => '#06b6d4', // cyan
                'order' => 5,
            ],
            [
                'name' => 'Communication',
                'slug' => 'communication',
                'description' => 'Prompts for team communication, summaries, and notifications',
                'icon' => 'ri-chat-3-line',
                'color' => '#ec4899', // pink
                'order' => 6,
            ],
            [
                'name' => 'Creative Tasks',
                'slug' => 'creative-tasks',
                'description' => 'Prompts for brainstorming, ideation, and creative problem-solving',
                'icon' => 'ri-lightbulb-line',
                'color' => '#f97316', // orange
                'order' => 7,
            ],
            [
                'name' => 'Risk Assessment',
                'slug' => 'risk-assessment',
                'description' => 'Prompts for identifying and evaluating project risks',
                'icon' => 'ri-alert-line',
                'color' => '#ef4444', // red
                'order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            PromptCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Prompt categories seeded successfully!');
    }
}
