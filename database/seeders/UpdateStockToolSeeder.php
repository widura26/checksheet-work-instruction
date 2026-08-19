<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tools;
use App\Models\UpdateStockTool;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UpdateStockToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::all();
        $tools = Tools::all();
        $projects = Project::all(); // Fetch all projects

        if ($users->isEmpty() || $tools->isEmpty() || $projects->isEmpty()) {
            $this->command->info('Please run UserSeeder, ToolSeeder and ProjectSeeder first.');

            return;
        }

        // Loop through some arbitrary number of updates, not necessarily tied to tools directly
        for ($i = 0; $i < 50; $i++) {
            $randomTool = $tools->random();
            $randomProject = $projects->random();
            $qtySebelum = $faker->numberBetween(10, 100);
            $qtyUpdate = $faker->numberBetween(1, 50);
            $qtySesudah = $qtySebelum + $qtyUpdate; // Assuming it's always an addition for 'tambah_stok_tool' or 'input_tool'

            UpdateStockTool::create([
                'user_id' => $users->random()->id,
                'tool_id' => $randomTool->id, // Use tool_id
                'project_id' => $randomProject->project_id, // Add project_id
                'qty_sebelum' => $qtySebelum,
                'qty_update' => $qtyUpdate,
                'qty_sesudah' => $qtySesudah,
                'tanggal_update' => $faker->dateTimeBetween('-1 year', 'now'),
                'type' => $faker->randomElement(['tambah_stok_tool', 'input_tool']), // Randomly choose type
            ]);
        }
    }
}
