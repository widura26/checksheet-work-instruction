<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Activitylog\Facades\Activity;

class DatabaseSeeder extends Seeder
{
    public function run(): void {
        Activity::disableLogging();
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class
        ]);
        Activity::enableLogging();
    }
}
