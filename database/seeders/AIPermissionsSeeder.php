<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AIPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates AI-specific permissions and assigns them to Super Admin role
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define AI permissions
        $permissions = [
            // AI Control Panel
            'access-ai-control', // Access AI control dashboard and view system status
            'manage-ai-settings', // Modify AI system settings and configuration
            
            // AI Decisions
            'view-ai-decisions', // View AI recommendations and decisions
            'approve-ai-actions', // Approve or reject AI suggestions
            
            // AI Prompts
            'manage-ai-prompts', // Edit AI prompt templates and manage versions
            'test-ai-prompts', // Test prompts in sandbox environment
            
            // AI Analytics
            'view-ai-analytics', // Access AI performance analytics and insights
            
            // AI Safety
            'manage-ai-safety', // Configure AI guardrails and safety settings
        ];

        // Create permissions
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }

        // Assign all AI permissions to Super Admin role
        try {
            $superAdmin = Role::findByName('Super Admin', 'web');
            $superAdmin->givePermissionTo($permissions);
            
            echo "✅ AI permissions created and assigned to Super Admin successfully!\n";
            echo "   Total permissions: " . count($permissions) . "\n";
        } catch (\Exception $e) {
            echo "⚠️  Warning: Could not assign permissions to Super Admin role.\n";
            echo "   Error: " . $e->getMessage() . "\n";
            echo "   Please assign manually or run RolesAndPermissionsSeeder first.\n";
        }

        echo "\n📋 AI Permissions Created:\n";
        foreach ($permissions as $perm) {
            echo "   - {$perm}\n";
        }
    }
}
