# AI Permissions Matrix - Documentation

**Version:** 1.0.0  
**Created:** 2026-01-07  
**Permission Count:** 8  
**Assigned to:** Super Admin (by default)

---

## Overview

This document details all AI-specific permissions in the system, their purpose, which routes they protect, and the recommended role assignments.

---

## AI Permissions List

### 1. access-ai-control

**Purpose:** Access the main AI control panel and view system status  
**Risk Level:** Low  
**Recommended Roles:** Admin, Manager

**Protected Routes:**
- `GET /admin/ai/control` - Main AI dashboard
- `POST /admin/ai/control/toggle` - Enable/disable AI system
- `GET /admin/ai/control/health` - System health check

**Use Case:**  
Users with this permission can view the AI control dashboard, see system metrics (total decisions, acceptance rate, pending reviews), and toggle the entire AI system on/off.

---

### 2. manage-ai-settings

**Purpose:** Modify AI system configuration and settings  
**Risk Level:** Medium  
**Recommended Roles:** Super Admin, Admin

**Protected Routes:**
- `GET /admin/ai/settings` - View settings page
- `POST /admin/ai/settings` - Update AI settings

**Use Case:**  
Users can configure AI system parameters like confidence thresholds, max priority jumps, cache TTL, fallback behavior, etc. Changes affect AI behavior globally.

**Settings Managed:**
- `ai_enabled` (boolean)
- `max_confidence_threshold` (float)
- `max_priority_jump` (int)
- `cache_ttl_settings` (int)
- `fallback_on_error` (boolean)

---

### 3. view-ai-decisions

**Purpose:** View AI recommendations and decisions  
**Risk Level:** Low  
**Recommended Roles:** All authenticated users

**Protected Routes:**
- `GET /admin/ai/decisions` - List all AI decisions
- `GET /admin/ai/decisions/{id}` - View specific decision details

**Use Case:**  
Users can browse all AI-generated recommendations, see the reasoning, confidence scores, and suggested actions. Read-only access.

**Decision Data Shown:**
- Decision type (task_analysis, project_breakdown, etc.)
- AI response (full JSON)
- Suggested actions
- Confidence score
- User action status (pending/accepted/rejected)

---

### 4. approve-ai-actions

**Purpose:** Approve, reject, or modify AI suggestions  
**Risk Level:** High  
**Recommended Roles:** Super Admin, Admin, Manager

**Protected Routes:**
- `GET /admin/ai/decisions/{id}/review` - Review decision
- `POST /admin/ai/decisions/{id}/accept` - Accept suggestion
- `POST /admin/ai/decisions/{id}/reject` - Reject suggestion
- `POST /admin/ai/decisions/{id}/modify` - Modify before accepting

**Use Case:**  
Users can take action on pending AI recommendations. Accepting a decision executes the suggested action (e.g., changes task priority). This permission embodies the Human-in-the-Loop principle.

**Actions Logged:**
- Which user approved/rejected
- Timestamp of action
- User's feedback/reason
- IP address and user agent

---

### 5. manage-ai-prompts

**Purpose:** Edit AI prompt templates and manage versions  
**Risk Level:** High  
**Recommended Roles:** Super Admin, AI Engineer

**Protected Routes:**
- `GET /admin/ai/prompts` - List prompts
- `GET /admin/ai/prompts/create` - Create new prompt
- `POST /admin/ai/prompts` - Store prompt
- `GET /admin/ai/prompts/{id}/edit` - Edit prompt
- `PUT /admin/ai/prompts/{id}` - Update prompt
- `DELETE /admin/ai/prompts/{id}` - Soft delete prompt
- `POST /admin/ai/prompts/{id}/restore` - Restore deleted prompt

**Use Case:**  
Users can create, edit, and version AI prompts. Changing prompts directly affects AI output quality and behavior.

**Prompt Fields:**
- Name (unique identifier)
- Type (system/user/assistant)
- Template (with {{variables}})
- Version (semantic versioning)
- Active status

---

### 6. test-ai-prompts

**Purpose:** Test prompts in sandbox environment  
**Risk Level:** Low  
**Recommended Roles:** Super Admin, Admin, AI Engineer

**Protected Routes:**
- `POST /admin/ai/prompts/{id}/test` - Test prompt with sample data

**Use Case:**  
Users can test prompt changes before activating them. The test runs in a sandbox (doesn't affect production data), returns AI response, and shows expected output.

**Test Features:**
- Live prompt editor
- Variable mock data input
- Real-time AI response preview
- Performance metrics (tokens, latency)

---

### 7. view-ai-analytics

**Purpose:** Access AI performance analytics and insights  
**Risk Level:** Low  
**Recommended Roles:** Admin, Manager

**Protected Routes:**
- `GET /admin/ai/insights` - AI insights dashboard
- `GET /admin/ai/analytics` - Detailed analytics
- `GET /admin/ai/analytics/export` - Export analytics data

**Use Case:**  
Users can view AI performance metrics, trends, and insights to understand how AI is helping (or not helping) the team.

**Analytics Shown:**
- Acceptance rate trends
- Confidence score distribution
- Most common decision types
- AI impact on productivity
- Prediction accuracy over time
- Cost analysis (API usage)

---

### 8. manage-ai-safety

**Purpose:** Configure AI guardrails and safety settings  
**Risk Level:** Critical  
**Recommended Roles:** Super Admin only

**Protected Routes:**
- `GET /admin/ai/safety` - Safety settings page
- `POST /admin/ai/safety/guardrails` - Update guardrails
- `POST /admin/ai/safety/fallback` - Configure fallback behavior

**Use Case:**  
Users can configure safety mechanisms that prevent AI from making dangerous suggestions.

**Guardrails:**
1. **Max Confidence Threshold** - Reject decisions below this confidence
2. **Max Priority Jump** - Limit how much AI can change priorities
3. **Max Actions Per Decision** - Cap number of suggested actions
4. **Blacklisted Actions** - Specific actions AI cannot suggest

**Fallback Configuration:**
- What to do when AI fails (show error / use defaults / suggest manual review)
- Monitoring and alerting thresholds

---

## Permission Matrix

| Role | access-ai-control | manage-ai-settings | view-ai-decisions | approve-ai-actions | manage-ai-prompts | test-ai-prompts | view-ai-analytics | manage-ai-safety |
|------|-------------------|--------------------|--------------------|--------------------|--------------------|------------------|-------------------|------------------|
| **Super Admin** | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Admin** | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ❌ |
| **Manager** | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ | ✅ | ❌ |
| **User** | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## Assigning Permissions

### Via Seeder (Automatic)

```bash
# Run AIPermissionsSeeder (assigns all to Super Admin)
php artisan db:seed --class=AIPermissionsSeeder
```

### Via Code

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// Assign single permission to role
$admin = Role::findByName('Admin');
$admin->givePermissionTo('access-ai-control');

// Assign multiple permissions
$manager = Role::findByName('Manager');
$manager->givePermissionTo([
    'access-ai-control',
    'view-ai-decisions',
    'approve-ai-actions',
    'view-ai-analytics'
]);

// Assign permission to specific user
$user = User::find(1);
$user->givePermissionTo('view-ai-decisions');
```

### Via Tinker

```bash
php artisan tinker

# Assign permission to role
$admin = Role::findByName('Admin');
$admin->givePermissionTo('manage-ai-prompts');

# Check if role has permission
$admin->hasPermissionTo('manage-ai-prompts'); // true

# List all permissions of a role
$admin->permissions->pluck('name');
```

---

## Checking Permissions

### In Controllers

```php
// Using @can directive (automatic via middleware)
public function index()
{
    // Middleware already checked 'access-ai-control' permission
    // ...
}

// Manual check
if (auth()->user()->can('approve-ai-actions')) {
    // Show approve button
}
```

### In Blade Templates

```blade
@can('access-ai-control')
    <a href="{{ route('ai.control.index') }}">AI Control Panel</a>
@endcan

@can('approve-ai-actions')
    <button onclick="acceptDecision({{ $decision->id }})">
        Accept AI Suggestion
    </button>
@endcan

@cannot('manage-ai-settings')
    <p class="text-muted">You don't have permission to modify AI settings.</p>
@endcannot
```

### In Routes

```php
// Permission-based route grouping
Route::middleware(['can:manage-ai-prompts'])->group(function () {
    Route::resource('prompts', AIPromptController::class);
});

// Multiple permissions (any)
Route::middleware(['can:manage-ai-settings,manage-ai-safety'])->group(function () {
    // User needs either permission
});
```

---

## Security Considerations

### 1. Least Privilege Principle
- Start by giving users minimal permissions
- Grant additional permissions based on need
- Review permission assignments quarterly

### 2. Audit Logging
All AI permission checks are logged via the `CheckAIPermission` middleware:
```php
activity('ai')
    ->causedBy(auth()->user())
    ->withProperties(['permission' => $permission, 'ip' => $request->ip()])
    ->log('ai_permission_denied'); // or 'ai_access_granted'
```

### 3. Critical Permissions
The following permissions should be VERY LIMITED:
- `manage-ai-safety` - Can disable guardrails (Super Admin only)
- `manage-ai-prompts` - Can change AI behavior significantly
- `approve-ai-actions` - Can execute AI suggestions

### 4. Permission Caching
Spatie Permission caches permissions. After changes, clear cache:
```bash
php artisan cache:forget spatie.permission.cache
# Or
php artisan permission:cache-reset
```

---

## Testing Permissions

### Unit Test Example

```php
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AIPermissionsTest extends TestCase
{
    public function test_super_admin_has_all_ai_permissions()
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->assertTrue($user->can('access-ai-control'));
        $this->assertTrue($user->can('manage-ai-settings'));
        $this->assertTrue($user->can('manage-ai-safety'));
    }

    public function test_regular_user_cannot_approve_ai_actions()
    {
        $user = User::factory()->create();
        $user->assignRole('User');

        $this->assertFalse($user->can('approve-ai-actions'));
    }

    public function test_ai_control_route_requires_permission()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/admin/ai/control');
        $response->assertStatus(403); // Forbidden

        $user->givePermissionTo('access-ai-control');
        $response = $this->actingAs($user)->get('/admin/ai/control');
        $response->assertStatus(200); // OK
    }
}
```

---

## Troubleshooting

### Permission Denied (403) Errors

**Problem:** User gets 403 even though they should have access.

**Solutions:**
1. Clear permission cache:
   ```bash
   php artisan permission:cache-reset
   ```

2. Check if permission exists:
   ```bash
   php artisan tinker
   >>> Permission::where('name', 'access-ai-control')->first();
   ```

3. Check user's permissions:
   ```bash
   php artisan tinker
   >>> User::find(1)->getAllPermissions()->pluck('name');
   ```

4. Assign permission manually:
   ```bash
   php artisan tinker
   >>> User::find(1)->givePermissionTo('access-ai-control');
   ```

---

## Future Enhancements

### Planned Permissions (Phase 3)
- `generate-ai-reports` - Auto-generate AI-powered reports
- `train-ai-model` - Upload training data for AI improvement
- `manage-ai-costs` - View and limit AI API usage costs
- `ai-batch-operations` - Run AI analysis on multiple items at once

---

**Document Status:** ✅ Complete  
**Last Updated:** 2026-01-07  
**Maintained By:** AI Development Team
