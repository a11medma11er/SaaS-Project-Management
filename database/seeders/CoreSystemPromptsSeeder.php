<?php

namespace Database\Seeders;

use App\Models\AI\AIPrompt;
use App\Models\AI\PromptCategory;
use Illuminate\Database\Seeder;

class CoreSystemPromptsSeeder extends Seeder
{
    /**
     * Core system prompts - THE HEART OF THE AI SYSTEM
     * These 15 prompts power all AI features and cannot be deleted
     */
    public function run(): void
    {
        $corePrompts = [
            
            // ============================================
            // AI FEATURES (4 prompts)
            // ============================================
            
            [
                'name' => 'ai_feature_development_plan',
                'category' => 'project-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Generates comprehensive development plans for projects with phases, timeline, and resource allocation',
                'template' => <<<'EOT'
Generate a comprehensive development plan for the following project:

**Project:** {{project_title}}
**Description:** {{project_description}}
**Budget:** {{budget}}
**Deadline:** {{deadline}}
**Team Size:** {{team_size}}
**Existing Tasks:** {{existing_tasks}}
**Progress:** {{progress}}%

Provide a structured development plan including:

1. **Executive Summary**
   - Project overview
   - Estimated timeline
   - Complexity assessment

2. **Development Phases**
   - Phase 1: Planning & Analysis (tasks, duration, deliverables)
   - Phase 2: Design (tasks, duration, deliverables)
   - Phase 3: Development (tasks, duration, deliverables)
   - Phase 4: Testing & QA (tasks, duration, deliverables)
   - Phase 5: Deployment (tasks, duration, deliverables)

3. **Timeline & Milestones**
   - Start date and estimated completion
   - Key milestones with dates
   - Critical path items

4. **Resource Requirements**
   - Team composition needed
   - Additional resources required
   - Budget allocation breakdown

5. **Risk Assessment**
   - Identified risks with severity
   - Mitigation strategies
   - Contingency plans

6. **Recommendations**
   - Best practices for this project
   - Optimization suggestions
   - Success criteria
EOT
            ],

            [
                'name' => 'ai_feature_task_analysis',
                'category' => 'task-management',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Analyzes individual tasks for complexity, effort estimation, and provides actionable recommendations',
                'template' => <<<'EOT'
Analyze the following task comprehensively:

**Task Title:** {{task_title}}
**Description:** {{task_description}}
**Priority:** {{priority}}
**Assigned To:** {{assigned_to}}
**Due Date:** {{due_date}}

Provide detailed analysis:

1. **Complexity Analysis**
   - Overall complexity level (Low/Medium/High/Very High)
   - Technical complexity factors
   - Business complexity factors

2. **Effort Estimation**
   - Estimated hours/days required
   - Breakdown by sub-components
   - Confidence level in estimate

3. **Dependencies**
   - Tasks that must be completed first
   - Tasks that depend on this task
   - External dependencies

4. **Risk Factors**
   - Technical risks
   - Resource risks
   - Timeline risks

5. **Recommendations**
   - Should this task be broken down further?
   - Suggested approach/methodology
   - Tools or resources needed
   - Priority adjustment suggestions
EOT
            ],

            [
                'name' => 'ai_feature_feasibility_study',
                'category' => 'project-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Generates detailed feasibility studies for project evaluation',
                'template' => <<<'EOT'
Generate a comprehensive feasibility study for:

**Project:** {{project_title}}
**Description:** {{project_description}}
**Proposed Budget:** {{budget}}
**Timeline:** {{timeline}}
**Team:** {{team_size}} members

Analyze and provide:

1. **Executive Summary**
   - Project viability assessment
   - Overall recommendation (Go/No-Go/Conditional)

2. **Technical Feasibility**
   - Technology stack assessment
   - Infrastructure requirements
   - Technical risks and challenges
   - Integration complexity

3. **Economic Feasibility**
   - Cost-benefit analysis
   - ROI estimation
   - Budget adequacy assessment
   - Financial risks

4. **Operational Feasibility**
   - Resource availability
   - Team capability assessment
   - Operational impact
   - Support requirements

5. **Schedule Feasibility**
   - Timeline realism assessment
   - Critical path analysis
   - Schedule risks
   - Milestone achievability

6. **Risk Analysis**
   - Major risks identified
   - Risk mitigation strategies
   - Success probability

7. **Final Recommendation**
   - Proceed/Modify/Reject with reasoning
   - Conditions for success
   - Next steps
EOT
            ],

            [
                'name' => 'ai_feature_project_breakdown',
                'category' => 'task-management',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Breaks down projects into detailed, actionable tasks organized by category and priority',
                'template' => <<<'EOT'
Break down the following project into detailed, actionable tasks:

**Project:** {{project_title}}
**Description:** {{project_description}}
**Granularity Level:** {{granularity}}
**Timeline:** {{deadline}}

Create a structured task breakdown:

1. **Planning & Analysis Tasks**
   - List 4-6 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

2. **Design Tasks**
   - List 4-6 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

3. **Development Tasks**
   - List 6-10 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

4. **Testing & QA Tasks**
   - List 4-5 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

5. **Deployment Tasks**
   - List 3-4 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

6. **Summary**
   - Total estimated tasks
   - Total estimated duration
   - Critical path
   - Resource requirements
EOT
            ],

            // ============================================
            // AI DECISION ENGINE (4 prompts)
            // ============================================

            [
                'name' => 'ai_decision_priority_suggestion',
                'category' => 'data-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Suggests optimal task priorities based on multiple factors and business rules',
                'template' => <<<'EOT'
Analyze and suggest priority for the following task:

**Task:** {{task_title}}
**Description:** {{task_description}}
**Current Priority:** {{current_priority}}
**Due Date:** {{due_date}}
**Project Priority:** {{project_priority}}
**Dependencies:** {{dependencies_count}} tasks
**Assigned To:** {{assigned_to}}

Consider these factors:

1. **Business Impact**
   - How critical is this task to business goals?
   - What's the urgency level?

2. **Dependencies**
   - How many tasks are blocked by this?
   - Is this task blocking critical path?

3. **Project Context**
   - Project priority level
   - Current project phase
   - Overall project health

4. **Resource Context**
   - Assignee availability
   - Required skills availability

Provide:
- **Suggested Priority:** Critical/High/Medium/Low
- **Reasoning:** Detailed explanation
- **Impact Assessment:** What happens if delayed
- **Confidence Level:** 0-100%
EOT
            ],

            [
                'name' => 'ai_decision_assignment_suggestion',
                'category' => 'task-management',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Recommends optimal user assignments based on workload, skills, and availability',
                'template' => <<<'EOT'
Suggest the best team member to assign this task:

**Task:** {{task_title}}
**Description:** {{task_description}}
**Required Skills:** {{required_skills}}
**Priority:** {{priority}}
**Estimated Effort:** {{estimated_effort}}

**Available Team Members:**
{{team_members}}

**Current Workload:**
{{workload_data}}

Analyze and recommend:

1. **Primary Recommendation**
   - Recommended assignee
   - Match score (0-100%)
   - Reasoning

2. **Alternative Options**
   - 2nd choice with reasoning
   - 3rd choice with reasoning

3. **Workload Analysis**
   - Current load of recommended assignee
   - Impact of this assignment
   - Overload risk assessment

4. **Skills Match**
   - How well skills align
   - Training needed (if any)
   - Experience level considerations

5. **Timeline Feasibility**
   - Can assignee complete by deadline?
   - Recommended adjustments if needed
EOT
            ],

            [
                'name' => 'ai_decision_deadline_estimation',
                'category' => 'task-management',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Estimates realistic deadlines based on complexity, workload, and historical data',
                'template' => <<<'EOT'
Estimate a realistic deadline for this task:

**Task:** {{task_title}}
**Description:** {{task_description}}
**Complexity:** {{complexity}}
**Priority:** {{priority}}
**Assignee:** {{assignee}}
**Assignee Current Load:** {{current_load}} tasks

Historical Context:
**Similar Tasks Average Duration:** {{avg_duration}}
**Team Velocity:** {{team_velocity}}

Provide estimation:

1. **Recommended Deadline**
   - Suggested date
   - Working days/hours estimated
   - Confidence level

2. **Estimation Breakdown**
   - Planning & analysis time
   - Development time
   - Testing time
   - Buffer time
   - Total time

3. **Risk Factors**
   - Factors that could delay
   - Probability of each risk
   - Mitigation suggestions

4. **Optimistic vs Realistic vs Pessimistic**
   - Best case scenario (date)
   - Most likely scenario (date)
   - Worst case scenario (date)

5. **Recommendations**
   - Should deadline be earlier or later?
   - Any adjustments needed to task scope
   - Resource allocation suggestions
EOT
            ],

            [
                'name' => 'ai_decision_risk_assessment',
                'category' => 'risk-assessment',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Assesses risks in AI decisions and suggests mitigation strategies',
                'template' => <<<'EOT'
Assess the risks of this AI decision:

**Decision Type:** {{decision_type}}
**Context:** {{decision_context}}
**Proposed Action:** {{proposed_action}}
**Impact Scope:** {{impact_scope}}

Analyze risks:

1. **Decision Risks**
   - What could go wrong?
   - Probability of each risk (High/Medium/Low)
   - Impact severity

2. **Dependency Risks**
   - Tasks/projects affected
   - Cascading impact assessment

3. **Resource Risks**
   - Team capacity impact
   - Skill gap risks
   - Availability concerns

4. **Timeline Risks**
   - Schedule impact
   - Deadline jeopardy
   - Critical path effects

5. **Mitigation Strategies**
   - For each identified risk
   - Preventive measures
   - Contingency plans

6. **Recommendation**
   - Proceed/Modify/Abort
   - Conditions for proceeding
   - Monitoring requirements
EOT
            ],

            // ============================================
            // AI AUTOMATION (4 prompts)
            // ============================================

            [
                'name' => 'ai_automation_workload_analysis',
                'category' => 'data-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Analyzes team workload distribution and identifies imbalances',
                'template' => <<<'EOT'
Analyze workload distribution across the team:

**Team Members and Their Tasks:**
{{team_workload_data}}

**Analysis Period:** {{period}}
**Total Tasks:** {{total_tasks}}
**Team Size:** {{team_size}}

Provide comprehensive analysis:

1. **Workload Distribution**
   - Tasks per team member
   - Hours per team member
   - Distribution graph/visualization data
   - Statistical summary (mean, median, std deviation)

2. **Imbalance Detection**
   - Overloaded members (>threshold)
   - Underutilized members (<threshold)
   - Severity of imbalance

3. **Capacity Analysis**
   - Total team capacity
   - Current utilization %
   - Available capacity
   - Bottlenecks identified

4. **Recommendations**
   - Which tasks should be redistributed?
   - From whom to whom?
   - Expected impact of changes
   - Priority of rebalancing

5. **Trend Analysis**
   - Is workload increasing/decreasing?
   - Projected capacity in X weeks
   - When will team be overloaded?
EOT
            ],

            [
                'name' => 'ai_automation_task_redistribution',
                'category' => 'ai-automation',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Suggests optimal task redistributions to balance team workload',
                'template' => <<<'EOT'
Suggest task redistribution to optimize team workload:

**Overloaded Member:** {{overloaded_user}}
**Current Load:** {{current_load}} tasks / {{hours}} hours
**Threshold:** {{threshold}}

**Their Tasks:**
{{user_tasks}}

**Available Team Members:**
{{available_members}}

Generate redistribution plan:

1. **Tasks to Redistribute**
   - List specific tasks
   - Reason for selecting each
   - Priority of redistribution

2. **Recommended Assignments**
   For each task:
   - Current assignee → New assignee
   - Match score (skills, availability)
   - Impact on new assignee's workload
   - Reasoning

3. **Impact Analysis**
   - Before redistribution state
   - After redistribution state
   - Workload balance improvement %
   - Risk assessment

4. **Implementation Plan**
   - Step-by-step redistribution sequence
   - Communication needed
   - Timeline for changes
   - Monitoring requirements

5. **Alternative Scenarios**
   - Plan B if primary fails
   - Hybrid approaches
EOT
            ],

            [
                'name' => 'ai_automation_overload_detection',
                'category' => 'ai-automation',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Detects team member overload conditions and triggers alerts',
                'template' => <<<'EOT'
Check for team overload conditions:

**Team Member:** {{user_name}}
**Current Tasks:** {{task_count}}
**Total Estimated Hours:** {{total_hours}}
**Work Hours Available:** {{available_hours}} per week
**Threshold:** {{threshold}} tasks or {{hour_threshold}} hours

**Task Details:**
{{task_list}}

Detect and report:

1. **Overload Status**
   - Is user overloaded? Yes/No
   - Severity: Low/Medium/High/Critical
   - By how much? (% over threshold)

2. **Contributing Factors**
   - Too many total tasks
   - Too many high-priority tasks
   - Unrealistic deadlines
   - Complex tasks requiring more time

3. **Impact Assessment**
   - At-risk tasks (likely to miss deadline)
   - Quality risk (rushed work)
   - Burnout risk
   - Project impact

4. **Immediate Actions Needed**
   - Urgent: Must be done now
   - Important: Should be done soon
   - Can wait: For next review cycle

5. **Recommended Solutions**
   - Task redistribution candidates
   - Deadline extension needs
   - Resource augmentation
   - Priority re-evaluation

6. **Alert Level**
   - None/Warning/Critical
   - Stakeholders to notify
   - Urgency of response
EOT
            ],

            [
                'name' => 'ai_automation_workflow_optimization',
                'category' => 'ai-automation',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Identifies workflow inefficiencies and suggests automation opportunities',
                'template' => <<<'EOT'
Analyze workflow for optimization opportunities:

**Process Name:** {{process_name}}
**Current Steps:** {{current_steps}}
**Frequency:** {{frequency}}
**Time Spent:** {{time_spent}} per cycle
**Pain Points:** {{pain_points}}

Analyze and optimize:

1. **Current State Analysis**
   - Process map
   - Bottlenecks identified
   - Redundant steps
   - Manual intervention points

2. **Inefficiency Detection**
   - Wasted time
   - Unnecessary steps
   - Context switching overhead
   - Waiting times

3. **Automation Opportunities**
   - Steps that can be automated
   - Tools/technologies for automation
   - Implementation complexity (Low/Med/High)
   - Expected time savings

4. **Optimized Workflow**
   - Proposed new process
   - Steps eliminated/combined
   - Automation points
   - Manual steps remaining

5. **ROI Analysis**
   - Time saved per cycle
   - Annual hours saved
   - Cost of implementation
   - Payback period

6. **Implementation Roadmap**
   - Phase 1: Quick wins
   - Phase 2: Medium complexity
   - Phase 3: Complex automations
   - Success metrics
EOT
            ],

            // ============================================
            // AI INSIGHTS & ANALYTICS (3 prompts)
            // ============================================

            [
                'name' => 'ai_insights_performance_analysis',
                'category' => 'data-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Analyzes team and project performance metrics with actionable insights',
                'template' => <<<'EOT'
Analyze performance metrics and provide insights:

**Analysis Period:** {{period}}
**Team:** {{team_name}}
**Projects:** {{project_count}}

**Performance Data:**
- Tasks Completed: {{completed_tasks}}
- Tasks In Progress: {{in_progress_tasks}}
- Tasks Overdue: {{overdue_tasks}}
- Average Completion Time: {{avg_completion_time}}
- Velocity: {{velocity}}

**Quality Metrics:**
- Bug Rate: {{bug_rate}}
- Rework %: {{rework_percentage}}
- Client Satisfaction: {{satisfaction_score}}

Provide analysis:

1. **Performance Summary**
   - Overall performance score (0-100)
   - Trend: Improving/Stable/Declining
   - Key highlights
   - Major concerns

2. **Detailed Metrics Analysis**
   - Velocity trends
   - Completion rate analysis
   - Quality trends
   - Productivity patterns

3. **Strengths Identified**
   - What's working well
   - Best performers
sing practices to replicate

4. **Issues Identified**
   - Performance gaps
   - Bottlenecks
   - At-risk areas
   - Root causes

5. **Predictions**
   - Next period forecast
   - Capacity projection
   - Risk areas

6. **Actionable Recommendations**
   - Top 3 improvement priorities
   - Specific action items
   - Expected impact
   - Implementation timeline
EOT
            ],

            [
                'name' => 'ai_insights_bottleneck_detection',
                'category' => 'data-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Identifies workflow bottlenecks and suggests resolution strategies',
                'template' => <<<'EOT'
Detect and analyze workflow bottlenecks:

**Project/Process:** {{process_name}}
**Analysis Period:** {{period}}

**Workflow Data:**
- Total Tasks: {{total_tasks}}
- Tasks in Queue: {{queued_tasks}}
- Average Wait Time: {{avg_wait_time}}
- Throughput: {{throughput}} tasks/week

**Stage-wise Distribution:**
{{stage_distribution}}

Detect bottlenecks:

1. **Bottleneck Identification**
   - Which stage/person/process is the bottleneck?
   - Severity: Minor/Major/Critical
   - Duration: How long has this existed?

2. **Impact Analysis**
   - Tasks delayed
   - Projects affected
   - Financial impact
   - Customer impact

3. **Root Cause Analysis**
   - Why is this a bottleneck?
   - Resource constraints?
   - Process inefficiency?
   - Dependency issues?

4. **Resolution Strategies**
   - Short-term fixes (1-2 weeks)
   - Medium-term solutions (1-2 months)
   - Long-term improvements (3+ months)

5. **Recommended Actions**
   - Immediate steps to take
   - Resource reallocation needs
   - Process changes required
   - Success metrics

6. **Prevention**
   - How to avoid in future
   - Early warning indicators
   - Monitoring recommendations
EOT
            ],

            [
                'name' => 'ai_insights_trend_prediction',
                'category' => 'data-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => 'Predicts project trends and future outcomes based on historical data',
                'template' => <<<'EOT'
Predict project trends and outcomes:

**Project:** {{project_name}}
**Current Status:** {{current_status}}
**Progress:** {{progress}}%
**Deadline:** {{deadline}}

**Historical Data:**
- Velocity (last 4 weeks): {{velocity_data}}
- Completion rates: {{completion_rates}}
- Bug trends: {{bug_trends}}
- Team capacity changes: {{capacity_changes}}

**Current Metrics:**
- Remaining tasks: {{remaining_tasks}}
- Avg task completion time: {{avg_time}}
- Team size: {{team_size}}
- Active issues: {{active_issues}}

Provide predictions:

1. **Completion Prediction**
   - Predicted completion date
   - Confidence level (%)
   - Will it meet deadline? Yes/No/Maybe
   - Days early/late

2. **Trend Analysis**
   - Velocity trend: Up/Down/Stable
   - Quality trend: Improving/Declining
   - Resource trend: Sufficient/Insufficient
   - Risk trend: Increasing/Decreasing

3. **Risk Predictions**
   - Probability of delay (%)
   - Probability of budget overrun (%)
   - Quality risk level
   - Resource shortage risk

4. **Scenario Analysis**
   - Best case: if everything goes well
   - Most likely: based on current trends
   - Worst case: if risks materialize

5. **Early Warnings**
   - Issues likely to emerge
   - When they'll likely occur
   - Preventive actions

6. **Recommendations**
   - Course corrections needed
   - Resource adjustments
   - Scope adjustments
   - Proactive measures
EOT
            ],
        ];

        $this->command->info('Seeding 15 core system prompts...');
        $created = 0;

        foreach ($corePrompts as $promptData) {
            // Get category
            $category = PromptCategory::where('slug', $promptData['category'])->first();
            
            // Extract variables from template
            preg_match_all('/\{\{([^}]+)\}\}/', $promptData['template'], $matches);
            $variables = array_unique($matches[1]);

            // Create or update prompt
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
                    'is_system' => $promptData['is_system'],
                    'usage_count' => 0,
                    'created_by' => 1, // System/Admin
                ]
            );

            $created++;
            $this->command->info("✓ {$promptData['name']}");
        }

        $this->command->info("\n🎉 Successfully seeded {$created} core system prompts!");
        $this->command->warn('⚠️  These prompts are protected and cannot be deleted (but can be edited).');
    }
}
