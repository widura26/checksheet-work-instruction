<?php

namespace Database\Seeders;

use App\Models\DaftarDistribusiTool;
use App\Models\DistribusiTool;
use App\Models\Tools;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DaftarDistribusiToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $distribusiTools = DistribusiTool::all();
        $tools = Tools::all();

        if ($distribusiTools->isEmpty() || $tools->isEmpty()) {
            $this->command->info('Please run DistribusiToolSeeder and ToolSeeder first.');

            return;
        }

        foreach ($distribusiTools as $distribusiTool) {
            // For each DistribusiTool, add 1 to 3 DaftarDistribusiTool entries
            $numberOfItems = $faker->numberBetween(1, 3);
            for ($i = 0; $i < $numberOfItems; $i++) {
                $tool = $tools->random();
                $jumlahDistribusi = $faker->numberBetween(1, 10);
                $qtyKembali = 0;
                $tanggalKembali = null;
                $status = null;

                // Adjust qty_kembali and status based on parent DistribusiTool's status
                if ($distribusiTool->status === 'kembali') {
                    $qtyKembali = $jumlahDistribusi;
                    $tanggalKembali = $distribusiTool->tanggal_kembali;
                    $status = 'kembali';
                } elseif ($distribusiTool->status === 'kembali_sebagian') {
                    $qtyKembali = $faker->numberBetween(0, $jumlahDistribusi - 1);
                    $tanggalKembali = $distribusiTool->tanggal_kembali;
                    $status = 'kembali_sebagian';
                } elseif ($distribusiTool->status === 'diserahkan') {
                    $status = 'diserahkan';
                } elseif ($distribusiTool->status === 'belum_kembali') {
                    $status = 'belum_kembali';
                }

                DaftarDistribusiTool::create([
                    'distribusi_tools_id' => $distribusiTool->id,
                    'tool_id' => $tool->id, // Changed from kode_material to tool_id
                    'jumlah_distribusi' => $jumlahDistribusi,
                    'qty_kembali' => $qtyKembali,
                    'tanggal_kembali' => $tanggalKembali,
                    'status' => $status,
                ]);
            }
        }
    }
}
