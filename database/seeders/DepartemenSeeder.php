<?php

namespace Database\Seeders;

use App\Models\Bagian;
use App\Models\Departemen;
// use App\Models\Posisi_CT;
use Illuminate\Database\Seeder;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //tambah CT_UU
        // Posisi_CT::create([
        //     'nama_posisi_ct' => 'CT_UU'
        // ]);
        // tambah departemen
        $ppc = Departemen::create(['nama_departemen' => 'Perencanaan dan Pengendalian Produksi Banyuwangi']);
        $fabrikasi = Departemen::create(['nama_departemen' => 'Fabrikasi Banyuwangi']);
        $finishing = Departemen::create(['nama_departemen' => 'Finishing Banyuwangi']);
        $distribusi = Departemen::create(['nama_departemen' => 'Distribusi Banyuwangi']);

        // tambah bagian
        // ppc
        $pfab = Bagian::create([
            'nama_bagian' => 'Pengendalian Fabrikasi Banyuwangi',
            'departemen_id' => $ppc->id,
        ]);
        $pfin = Bagian::create([
            'nama_bagian' => 'Pengendalian Finishing Banyuwangi',
            'departemen_id' => $ppc->id,
        ]);
        $pPem = Bagian::create([
            'nama_bagian' => 'Pengendalian Pemeliharaan Banyuwangi',
            'departemen_id' => $ppc->id,
        ]);
        // fabrikasi
        $metalWorking = Bagian::create([
            'nama_bagian' => 'Metal Working, Machining And Minor Assembling Banyuwangi',
            'departemen_id' => $fabrikasi->id,
        ]);
        $subA = Bagian::create([
            'nama_bagian' => 'Sub Assembling Banyuwangi',
            'departemen_id' => $fabrikasi->id,
        ]);
        $subAssembling = Bagian::create([
            'nama_bagian' => 'Carbody Banyuwangi',
            'departemen_id' => $fabrikasi->id,
        ]);
        // finishing
        $pengecatan = Bagian::create([
            'nama_bagian' => 'Pengecatan Dan Interior Banyuwangi',
            'departemen_id' => $finishing->id,
        ]);
        $instalasiMekanik = Bagian::create([
            'nama_bagian' => 'Instalasi Mekanik Banyuwangi',
            'departemen_id' => $finishing->id,
        ]);
        $instalasiElektrik = Bagian::create([
            'nama_bagian' => 'Instalasi Elektrik Banyuwangi',
            'departemen_id' => $finishing->id,
        ]);
        // // tambah unit
        // fabrikasi
        $pfab->unit()->create(['nama_unit' => 'Steel Work dan Minor Assy Banyuwangi']);
        $pfab->unit()->create(['nama_unit' => 'Sub Assy Dan Carbody Assy Banyuwangi']);
        // finishing
        $pfin->unit()->create(['nama_unit' => 'Line Feeding Banyuwangi']);
        $pfin->unit()->create(['nama_unit' => 'Transportasi Kereta Banyuwangi']);
        // pemeliharaan
        $pPem->unit()->create(['nama_unit' => 'Pemeliharaan Mesin Dan Alat Angkat Angkut Banyuwangi']);
        $pPem->unit()->create(['nama_unit' => 'Pemeliharaan Gedung Dan Fasilitas Banyuwangi']);
        // metal working
        $metalWorking->unit()->create(['nama_unit' => 'Metal Working and Machining Banyuwangi']);
        $metalWorking->unit()->create(['nama_unit' => 'Minor Assembling Banyuwangi']);
        // sub assembling
        $subA->unit()->create(['nama_unit' => 'Roof Assembling Banyuwangi']);
        $subA->unit()->create(['nama_unit' => 'Sidewall And Endwall Assembling Banyuwangi']);
        $subA->unit()->create(['nama_unit' => 'Underframe Banyuwangi']);
        // carbody
        $subAssembling->unit()->create(['nama_unit' => 'Carbody Assembling Banyuwangi']);
        $subAssembling->unit()->create(['nama_unit' => 'Fitting Accessories and Ceiling Banyuwangi']);
        // pengecatan
        $pengecatan->unit()->create(['nama_unit' => 'Pengecatan Banyuwangi']);
        $pengecatan->unit()->create(['nama_unit' => 'Komponen Pemasangan Interior Banyuwangi']);
        $pengecatan->unit()->create(['nama_unit' => 'Pemasangan Panel Interior Banyuwangi']);
        // instalasi mekanik
        $instalasiMekanik->unit()->create(['nama_unit' => 'Instalasi Komponen Mekanik Banyuwangi']);
        $instalasiMekanik->unit()->create(['nama_unit' => 'Instalasi Perpipaan Banyuwangi']);
        $instalasiMekanik->unit()->create(['nama_unit' => 'Bogie Assembling Banyuwangi']);
        // instalasi elektrik
        $instalasiElektrik->unit()->create(['nama_unit' => 'Instalasi Kabel dan Routing Harness Banyuwangi']);
        $instalasiElektrik->unit()->create(['nama_unit' => 'Pemasangan Komponen Elektrik Banyuwangi']);
        $instalasiElektrik->unit()->create(['nama_unit' => 'Koneksi Komponen Elektrik Banyuwangi']);
    }
}
