<?php

namespace Database\Seeders;

use App\Models\Bagian;
use App\Models\Departemen;
use Illuminate\Database\Seeder; // Add this line

class BagianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $ppc = Departemen::where('nama_departemen', 'ppc')->first();
        $fabrikasi = Departemen::where('nama_departemen', 'fabrikasi')->first();
        $finishing = Departemen::where('nama_departemen', 'finishing')->first();

        Bagian::create(['nama_bagian' => 'Fabrikasi Gedung I1', 'lokasi' => 'I1', 'departemen_id' => $fabrikasi->id ?? null]);
        Bagian::create(['nama_bagian' => 'Fabrikasi Gedung I2', 'lokasi' => 'I2', 'departemen_id' => $fabrikasi->id ?? null]);
        Bagian::create(['nama_bagian' => 'Finishing N1', 'lokasi' => 'N1', 'departemen_id' => $finishing->id ?? null]);
        Bagian::create(['nama_bagian' => 'Finishing N3', 'lokasi' => 'N3', 'departemen_id' => $finishing->id ?? null]);
    }
}
