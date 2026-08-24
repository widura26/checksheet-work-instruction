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
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class KaryawanImport implements ToCollection, WithHeadingRow, WithChunkReading, WithValidation, WithBatchInserts, WithSkipDuplicates, SkipsEmptyRows, WithCustomCsvSettings
{
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => null,
            'test_auto_detect' => true,
            'input_encoding' => 'UTF-8',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $nip = $row['nip'] ?? null;
            $nama = $row['nama'] ?? null;

            if (empty($nip) || empty($nama)) {
                continue;
            }

            $departemen = $row['departemen'] ?? null;
            $bagian = $row['bagian'] ?? null;
            $unit = $row['unit'] ?? null;

            $departemenId = $departemen
                ? Departemen::where('nama_departemen', 'LIKE', '%' . strtolower($departemen) . '%')->first()->id ?? null
                : null;
            $bagianId = ($bagian && $departemenId)
                ? Bagian::where('nama_bagian', 'LIKE', '%' . strtolower($bagian) . '%')->where('departemen_id', $departemenId)->first()->id ?? null
                : null;
            $unitId = ($unit && $bagianId)
                ? Unit::where('nama_unit', 'LIKE', '%' . strtolower($unit) . '%')->where('bagian_id', $bagianId)->first()->id ?? null
                : null;

            Karyawan::firstOrCreate(
                ['nip' => $nip],
                [
                    'nama' => $nama,
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
        ];
    }
    public function batchSize(): int
    {
        return 100;
    }
}
