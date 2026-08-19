<?php

namespace Database\Seeders;

use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use Illuminate\Database\Seeder;

class ToolProjectStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::all();
        $tools = Tools::all();
        $positions = Posisi_CT::all();

        if ($projects->isEmpty() || $tools->isEmpty() || $positions->isEmpty()) {
            $this->command->info('Please run ProjectSeeder, ToolSeeder, and Posisi_CTSeeder first.');

            return;
        }

        // Clear existing stock to avoid duplicates on re-seed
        ToolProjectStock::truncate();

        // Create 20 stock entries
        for ($i = 0; $i < 20; $i++) {
            $project = $projects->random();
            $tool = $tools->random();
            $position = $positions->random();

            // Use updateOrCreate to avoid unique constraint violations if the loop randomly picks the same combination twice
            ToolProjectStock::updateOrCreate(
                [
                    'project_id' => $project->project_id,
                    'tool_id' => $tool->id,
                    'posisi_ct_id' => $position->id,
                ],
                [
                    'qty' => rand(10, 100),
                ]
            );
        }

        $this->command->info('ToolProjectStockSeeder completed.');
    }
}
