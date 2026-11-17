<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\Activity;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void {
        Activity::disableLogging();
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class
        ]);
        Activity::enableLogging();
    }
}
