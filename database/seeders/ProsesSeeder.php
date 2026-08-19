<?php

namespace Database\Seeders;

use App\Models\Proses;
use Illuminate\Database\Seeder;

class ProsesSeeder extends Seeder
{
    public function run(): void
    {
        $proses = [
            ['id_proses' => 'P01', 'kode_proses' => 'CUT', 'nama' => 'Cutting', 'id_kel' => 'K1'],
            ['id_proses' => 'P02', 'kode_proses' => 'BEND', 'nama' => 'Bending', 'id_kel' => 'K1'],
            ['id_proses' => 'P03', 'kode_proses' => 'WELD', 'nama' => 'Welding', 'id_kel' => 'K2'],
            ['id_proses' => 'P04', 'kode_proses' => 'GRIND', 'nama' => 'Grinding', 'id_kel' => 'K2'],
            ['id_proses' => 'P05', 'kode_proses' => 'DRILL', 'nama' => 'Drilling', 'id_kel' => 'K3'],
            ['id_proses' => 'P06', 'kode_proses' => 'ASSY', 'nama' => 'Assembly', 'id_kel' => 'K3'],
            ['id_proses' => 'P07', 'kode_proses' => 'PAINT', 'nama' => 'Painting', 'id_kel' => 'K4'],
            ['id_proses' => 'P08', 'kode_proses' => 'FINAL', 'nama' => 'Final Inspection', 'id_kel' => 'K4'],
        ];

        foreach ($proses as $p) {
            Proses::updateOrCreate(['kode_proses' => $p['kode_proses']], $p);
        }
    }
}
