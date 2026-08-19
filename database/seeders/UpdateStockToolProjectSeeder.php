<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\UpdateStockTool;
use App\Models\UpdateStockToolProject;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UpdateStockToolProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $updateStockTools = UpdateStockTool::all();
        $projects = Project::all();

        if ($updateStockTools->isEmpty() || $projects->isEmpty()) {
            $this->command->info('Please run UpdateStockToolSeeder and ProjectSeeder first.');

            return;
        }

        // Create 20 records
        for ($i = 0; $i < 20; $i++) {
            UpdateStockToolProject::create([
                'update_stock_tools_id' => $updateStockTools->random()->id,
                'project_id' => $projects->random()->project_id,
            ]);
        }
    }
}
