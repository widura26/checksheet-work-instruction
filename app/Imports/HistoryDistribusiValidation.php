<?php

namespace App\Imports;

use App\Models\DaftarKodeMaterial;
use App\Models\Karyawan;
use App\Models\Tools;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HistoryDistribusiValidation implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // karena heading
            if (! isset($row['kode_material']) || trim($row['kode_material']) === '') {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => null,
                    'error' => 'Kode material wajib diisi',
                ];

                continue;
            }
            if (! isset($row['nip']) || trim($row['nip']) === '') {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => null,
                    'error' => 'NIP wajib diisi',
                ];

                continue;
            }
            if (! Karyawan::where('nip', trim((string) $row['nip']))->exists()) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => $row['nip'],
                    'error' => 'NIP tidak ditemukan',
                ];
                // continue;
            }
            if (! DaftarKodeMaterial::where(
                'kode_material',
                trim($row['kode_material'])
            )->exists()) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => $row['kode_material'],
                    'error' => 'Kode material tidak ditemukan',
                ];
            }
            if (! Tools::where(
                'kode_material',
                trim($row['kode_material'])
            )->exists()) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => $row['kode_material'],
                    'error' => 'Kode material tidak ditemukan Di Stok CT',
                ];
            }
        }
    }
}
