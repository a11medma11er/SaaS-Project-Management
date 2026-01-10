<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DatabaseFreshProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fresh-production 
                            {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables, run migrations, and seed with production data only (no demo data)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Safety check for production environment
        if (app()->environment('production') && !$this->option('force')) {
            $this->error('⛔ Cannot run this command in production without --force flag!');
            $this->warn('💡 Use: php artisan db:fresh-production --force');
            return 1;
        }

        $this->warn('⚠️  This will DROP ALL TABLES and recreate the database!');
        
        if (!$this->confirm('Do you want to continue?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('🗑️  Dropping all tables...');
        Artisan::call('migrate:fresh', [], $this->output);

        $this->info('📦 Seeding production data (NO demo data)...');
        Artisan::call('db:seed', [
            '--class' => 'ProductionSeeder'
        ], $this->output);

        $this->newLine();
        $this->info('✅ Database refreshed with production data!');
        $this->warn('⚠️  No demo data was seeded.');
        
        return 0;
    }
}
