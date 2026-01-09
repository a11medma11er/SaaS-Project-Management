<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Assign Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
            $this->command->info('Super Admin user created and assigned Super Admin role!');
        } else {
            $this->command->error('Super Admin role not found! Please run RolesAndPermissionsSeeder first.');
        }

        // Create additional demo users (optional)
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin.user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }

        $manager = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            $manager->assignRole($managerRole);
        }

        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $user->assignRole($userRole);
        }

        // Create 16 additional users (for total of 20)
        $roles = Role::whereIn('name', ['Manager', 'User'])->get();
        
        $departments = ['Development', 'Design', 'Marketing', 'QA', 'DevOps', 'Support'];
        $positions = ['Senior', 'Junior', 'Lead', 'Staff'];
        
        $this->command->info('Creating 16 additional users...');
        
        for ($i = 1; $i <= 16; $i++) {
            $department = $departments[array_rand($departments)];
            $position = $positions[array_rand($positions)];
            
            $newUser = User::create([
                'name' => $position . ' ' . $department . ' ' . $i,
                'email' => strtolower($position . '.' . $department . $i . '@example.com'),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            
            // Assign random role (Manager or User)
            $randomRole = $roles->random();
            $newUser->assignRole($randomRole);
        }

        $this->command->info('All demo users created successfully!');
        $this->command->info('Total Users: 20');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Super Admin - Email: admin@example.com, Password: password');
        $this->command->info('Admin - Email: admin.user@example.com, Password: password');
        $this->command->info('Manager - Email: manager@example.com, Password: password');
        $this->command->info('User - Email: user@example.com, Password: password');
        $this->command->info('');
        $this->command->info('Additional 16 users created with various roles and departments');
        $this->command->info('Email format: [position].[department][number]@example.com');
        $this->command->info('Password: password (for all users)');
    }
}
