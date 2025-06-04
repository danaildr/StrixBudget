<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\SampleDataSeeder;

class CreateSampleData extends Command
{
    protected $signature = 'app:create-sample-data';
    protected $description = 'Create sample data for development';

    public function handle()
    {
        $this->info('Creating sample data...');
        
        $seeder = new SampleDataSeeder();
        $seeder->run();

        $this->info('Sample data created successfully!');
    }
} 