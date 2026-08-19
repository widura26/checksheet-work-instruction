<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Contracts\Queue\ShouldQueue;
class KodeMaterialImport implements ToCollection, WithBatchInserts, WithChunkReading, WithStartRow, SkipsEmptyRows, WithHeadingRow,ShouldQueue
{
    public function collection($rows)
    {
        $existing = DB::table('daftar_kode_materials')
            ->pluck('kode_material')
            ->flip();

        $insertData = [];

        foreach ($rows as $row) {
            $kode = $row['kode_material'];
            if (isset($existing[$kode])) {
                continue;
            }

            $insertData[] = [
                'kode_material' => $kode,
                'satuan' => $row['bun'],
                'deskripsi' => $row['deskripsi'],
                'spesifikasi' => $row['spesifikasi'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $existing[$kode] = true;
        }

        // Bulk insert
        if (! empty($insertData)) {
            DB::table('daftar_kode_materials')->insert($insertData);
        }
      
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 2000; // membaca per 2k
    }

    public function batchSize(): int
    {
        return 1000; // insert per 1k
    }
}
