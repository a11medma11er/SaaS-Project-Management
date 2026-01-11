<?php

namespace Database\Seeders;

use App\Models\AI\AIPrompt;
use App\Models\AI\PromptCategory;
use App\Models\AI\PromptTag;
use Illuminate\Database\Seeder;

class PromptLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prompts = [
            // Task Management
            [
                'name' => 'task_complexity_analysis',
                'category' => 'task-management',
                'tags' => ['analysis', 'estimation'],
                'type' => 'system',
                'description' => 'Analyzes task complexity and provides effort estimation',
                'template' => <<<'EOT'
Analyze the following task and provide a complexity assessment:

**Task Title:** {{task_title}}
**Description:** {{task_description}}
**Estimated Hours:** {{estimated_hours}}

Please provide:
1. Complexity Level (Low/Medium/High/Very High)
2. Key Challenges
3. Required Skills
4. Potential Risks
5. Recommended Approach
6. Estimated Time Breakdown
EOT
            ],
            [
                'name' => 'task_breakdown',
                'category' => 'task-management',
                'tags' => ['planning', 'automation'],
                'type' => 'system',
                'description' => 'Breaks down a large task into smaller sub-tasks',
                'template' => <<<'EOT'
Break down the following task into smaller, manageable sub-tasks:

**Project:** {{project_name}}
**Main Task:** {{task_title}}
**Description:** {{task_description}}
**Timeline:** {{deadline}}

Provide:
1. List of sub-tasks (5-10 items)
2. Dependencies between sub-tasks
3. Estimated duration for each
4. Priority level for each
5. Assigned skills/roles needed
EOT
            ],

            // Project Analysis
            [
                'name' => 'project_feasibility_study',
                'category' => 'project-analysis',
                'tags' => ['analysis', 'planning', 'business'],
                'type' => 'system',
                'description' => 'Generates comprehensive feasibility study for projects',
                'template' => <<<'EOT'
Generate a comprehensive feasibility study for the following project:

**Project Name:** {{project_name}}
**Description:** {{project_description}}
**Budget:** {{budget}}
**Timeline:** {{timeline}}
**Team Size:** {{team_size}}

Include:
1. Executive Summary
2. Technical Feasibility
3. Economic Feasibility
4. Operational Feasibility
5. Resource Requirements
6. Risk Analysis
7. Recommendations
EOT
            ],
            [
                'name' => 'project_risk_assessment',
                'category' => 'risk-assessment',
                'tags' => ['risk-management', 'analysis'],
                'type' => 'system',
                'description' => 'Identifies and evaluates project risks',
                'template' => <<<'EOT'
Perform a comprehensive risk assessment for:

**Project:** {{project_name}}
**Current Status:** {{project_status}}
**Key Milestones:** {{milestones}}
**Team Capacity:** {{team_capacity}}

Identify:
1. Technical Risks (High/Medium/Low)
2. Resource Risks
3. Timeline Risks
4. Budget Risks
5. External Dependencies
6. Mitigation Strategies for each risk
7. Contingency Plans
EOT
            ],

            // Content Generation
            [
                'name' => 'meeting_summary',
                'category' => 'communication',
                'tags' => ['documentation', 'collaboration'],
                'type' => 'user',
                'description' => 'Generates structured meeting summaries',
                'template' => <<<'EOT'
Create a professional meeting summary from the following notes:

**Meeting Date:** {{meeting_date}}
**Attendees:** {{attendees}}
**Topics Discussed:** {{topics}}
**Decisions Made:** {{decisions}}

Format the summary with:
1. Meeting Overview
2. Key Discussion Points
3. Decisions and Actions
4. Action Items with Owners
5. Next Steps
6. Follow-up Required
EOT
            ],
            [
                'name' => 'email_draft',
                'category' => 'communication',
                'tags' => ['documentation', 'business'],
                'type' => 'user',
                'description' => 'Drafts professional emails for various purposes',
                'template' => <<<'EOT'
Draft a professional email with the following details:

**To:** {{recipient}}
**Subject:** {{subject}}
**Purpose:** {{purpose}}
**Key Points:** {{key_points}}
**Tone:** {{tone}}

Requirements:
- Professional and clear language
- Appropriate greeting and closing
- Concise but comprehensive
- Action-oriented if needed
EOT
            ],

            // AI Automation
            [
                'name' => 'workflow_automation_proposal',
                'category' => 'ai-automation',
                'tags' => ['automation', 'optimization', 'planning'],
                'type' => 'system',
                'description' => 'Proposes workflow automation opportunities',
                'template' => <<<'EOT'
Analyze the following workflow and propose automation opportunities:

**Process Name:** {{process_name}}
**Current Steps:** {{steps}}
**Frequency:** {{frequency}}
**Pain Points:** {{pain_points}}

Provide:
1. Automation Opportunities
2. Recommended Tools/Technologies
3. Implementation Complexity
4. Expected Time Savings
5. ROI Estimate
6. Implementation Roadmap
EOT
            ],

            // Data Analysis
            [
                'name' => 'performance_analysis',
                'category' => 'data-analysis',
                'tags' => ['analysis', 'reporting'],
                'type' => 'system',
                'description' => 'Analyzes performance metrics and trends',
                'template' => <<<'EOT'
Analyze the following performance data:

**Period:** {{period}}
**Metrics:** {{metrics}}
**Previous Period Data:** {{previous_data}}
**Current Period Data:** {{current_data}}

Provide:
1. Key Performance Indicators
2. Trends and Patterns
3. Notable Changes
4. Performance Gaps
5. Recommendations for Improvement
6. Action Plan
EOT
            ],

            // Creative Tasks
            [
                'name' => 'brainstorming_facilitator',
                'category' => 'creative-tasks',
                'tags' => ['brainstorming', 'planning'],
                'type' => 'user',
                'description' => 'Facilitates brainstorming sessions with structured questions',
                'template' => <<<'EOT'
Facilitate a brainstorming session for:

**Challenge/Goal:** {{challenge}}
**Context:** {{context}}
**Constraints:** {{constraints}}

Generate:
1. 10 Creative Ideas/Solutions
2. Pros and Cons for each
3. Feasibility Rating (1-10)
4. Innovation Score (1-10)
5. Top 3 Recommended Ideas with reasoning
EOT
            ],
        ];

        foreach ($prompts as $promptData) {
            // Get category
            $category = PromptCategory::where('slug', $promptData['category'])->first();
            
            // Extract variables from template
            preg_match_all('/\{\{([^}]+)\}\}/', $promptData['template'], $matches);
            $variables = array_unique($matches[1]);

            // Create prompt
            $prompt = AIPrompt::updateOrCreate(
                ['name' => $promptData['name']],
                [
                    'name' => $promptData['name'],
                    'type' => $promptData['type'],
                    'category_id' => $category?->id,
                    'template' => $promptData['template'],
                    'version' => '1.0.0',
                    'variables' => $variables,
                    'description' => $promptData['description'],
                    'is_active' => true,
                    'usage_count' => 0,
                    'created_by' => 1, // Assuming admin user ID is 1
                ]
            );

            // Attach tags
            if (isset($promptData['tags'])) {
                $tagIds = PromptTag::whereIn('slug', $promptData['tags'])->pluck('id');
                $prompt->tags()->sync($tagIds);
            }
        }

        $this->command->info('Prompt library seeded successfully with ' . count($prompts) . ' templates!');
    }
}
