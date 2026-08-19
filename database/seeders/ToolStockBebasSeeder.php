<?php

namespace Database\Seeders;

use App\Models\Tools;
use App\Models\ToolStockBebas;
use Faker\Factory as Faker; // Assuming you have a Tools model to get kode_material
use Illuminate\Database\Seeder;

class ToolStockBebasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        $tools = Tools::all(); // Get all existing tools

        if ($tools->isEmpty()) {
            $this->command->info('Please run ToolSeeder first to populate tools.');

            return;
        }

        // To ensure unique kode_material, we'll use a collection of kode_material from Tools
        $seededKodeMaterials = [];

        for ($i = 0; $i < 20; $i++) { // Create up to 20 unique ToolStockBebas records
            $tool = $tools->random();
            $kodeMaterial = $tool->kode_material;

            // Ensure unique kode_material for ToolStockBebas
            if (! in_array($kodeMaterial, $seededKodeMaterials)) {
                ToolStockBebas::create([
                    'kode_material' => $kodeMaterial,
                    'qty' => $faker->numberBetween(1, 100),
                ]);
                $seededKodeMaterials[] = $kodeMaterial;
            }

            if (count($seededKodeMaterials) >= $tools->count()) {
                $this->command->info('All unique kode_material from tools have been used for ToolStockBebas.');
                break;
            }
        }
    }
}
