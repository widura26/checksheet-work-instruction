<?php

namespace Database\Seeders;

use App\Models\DetailKerusakanTool;
use App\Models\PenguranganTool;
use App\Models\Tools;
use App\Models\User;
use Faker\Factory as Faker; // Add this line
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PenguranganToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $users = User::all();
        $tools = Tools::all();
        $detailKerusakanTools = DetailKerusakanTool::all(); // Fetch all DetailKerusakanTool records

        if ($users->isEmpty() || $tools->isEmpty()) {
            $this->command->info('Please run UserSeeder and ToolSeeder first.');

            return;
        }

        for ($i = 0; $i < 50; $i++) { // Create 50 PenguranganTool records
            $randomTool = $tools->random();
            $qtySebelum = $faker->numberBetween(10, 100); // Random initial quantity
            $qtyDikurangi = $faker->numberBetween(1, $qtySebelum); // Reduce by a random amount
            $qtySesudah = $qtySebelum - $qtyDikurangi;

            $detailKerusakanToolId = null;
            if ($detailKerusakanTools->isNotEmpty() && $faker->boolean(30)) { // 30% chance to link to a KerusakanTool
                $detailKerusakanToolId = $detailKerusakanTools->random()->id;
            }
            PenguranganTool::create([
                'public_id' => Str::uuid(),
                'user_id' => $users->random()->id,
                'tool_id' => $randomTool->id, // Use tool_id instead of kode_material
                'detail_kerusakan_tools_id' => $detailKerusakanToolId, // Add this line
                'qty_sebelum' => $qtySebelum,
                'qty_dikurangi' => $qtyDikurangi,
                'qty_sesudah' => $qtySesudah,
                'alasan' => $faker->sentence(),
                'document_path' => 'documents/pengurangan/'.$faker->uuid().'.pdf',
                'tanggal_pengurangan' => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
        }
    }
}
