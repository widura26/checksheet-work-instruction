<?php

namespace App\Imports;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithSkipDuplicates;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class KaryawanImport implements ToCollection, WithHeadingRow, WithChunkReading, WithValidation, WithBatchInserts, WithSkipDuplicates, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['unit'])) {
                continue;
            }
            $departemenId = Departemen::where('nama_departemen', 'LIKE', '%' . strtolower($row['departemen']) . '%')->first()->id ?? null;
            $bagianId = Bagian::where('nama_bagian', 'LIKE', '%' . strtolower($row['bagian']) . '%')->where('departemen_id', $departemenId)->first()->id ?? null;
            $unitId = Unit::where('nama_unit', 'LIKE', '%' . strtolower($row['unit']) . '%')->where('bagian_id', $bagianId)->first()->id ?? null;
            Karyawan::firstOrCreate(
                ['nip' => $row['nip']],
                [
                    'nama' => $row['nama'],
                    'departemen_id' => $departemenId,
                    'bagian_id' => $bagianId,
                    'unit_id' => $unitId,
                    'jabatan' => $row['jabatan'] ?? null,
                ]
            );
        }
    }
    public function chunkSize(): int
    {
        return 100;
    }
    public function rules(): array
    {
        return [
            'nip' => [
                'required',
            ],
            'nama' => [
                'required',
            ],
            'departemen' => [
                'required',
            ],
        ];
    }
    public function batchSize(): int
    {
        return 100;
    }
}
