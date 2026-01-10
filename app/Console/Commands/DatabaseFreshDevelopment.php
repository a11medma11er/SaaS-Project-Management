<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DatabaseFreshDevelopment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fresh-development';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables, run migrations, and seed with full demo data for development';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Prevent running in production
        if (app()->environment('production')) {
            $this->error('⛔ This command cannot be run in production!');
            $this->warn('💡 Use db:fresh-production instead.');
            return 1;
        }

        $this->warn('⚠️  This will DROP ALL TABLES and recreate the database!');
        
        if (!$this->confirm('Do you want to continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('🗑️  Dropping all tables...');
        Artisan::call('migrate:fresh', [], $this->output);

        $this->info('📦 Seeding development data (with demo data)...');
        
        // Set environment variable temporarily for this command
        putenv('SEED_DEMO_DATA=true');
        
        Artisan::call('db:seed', [], $this->output);

        $this->newLine();
        $this->info('✅ Database refreshed with full demo data!');
        $this->line('📊 You now have sample projects, tasks, users, and AI data.');
        
        return 0;
    }
}
