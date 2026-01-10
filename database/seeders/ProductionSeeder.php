<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Production Seeder
 * 
 * Only seeds essential data required for production:
 * - Roles and Permissions
 * - AI Permissions
 * - Default Admin User
 * 
 * NO DEMO DATA
 */
class ProductionSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🚀 Running Production Seeder (No Demo Data)...');

        // Essential seeders only
        $this->call([
            RolesAndPermissionsSeeder::class,
            AIPermissionsSeeder::class,
            DefaultUserSeeder::class,
        ]);

        $this->command->info('✅ Production seeding completed!');
        $this->command->warn('⚠️  No demo data was seeded.');
    }
}
