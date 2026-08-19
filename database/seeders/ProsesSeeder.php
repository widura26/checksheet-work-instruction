<?php

namespace Database\Seeders;

use App\Models\Proses;
use Illuminate\Database\Seeder;

class ProsesSeeder extends Seeder
{
    public function run(): void
    {
        $proses = [
            ['kode_proses' => 'CUT', 'nama' => 'Cutting', 'id_kel' => 'K1'],
            ['kode_proses' => 'BEND', 'nama' => 'Bending', 'id_kel' => 'K1'],
            ['kode_proses' => 'WELD', 'nama' => 'Welding', 'id_kel' => 'K2'],
            ['kode_proses' => 'GRIND', 'nama' => 'Grinding', 'id_kel' => 'K2'],
            ['kode_proses' => 'DRILL', 'nama' => 'Drilling', 'id_kel' => 'K3'],
            ['kode_proses' => 'ASSY', 'nama' => 'Assembly', 'id_kel' => 'K3'],
            ['kode_proses' => 'PAINT', 'nama' => 'Painting', 'id_kel' => 'K4'],
            ['kode_proses' => 'FINAL', 'nama' => 'Final Inspection', 'id_kel' => 'K4'],
        ];

        foreach ($proses as $p) {
            Proses::updateOrCreate(['kode_proses' => $p['kode_proses']], $p);
        }
    }
}
