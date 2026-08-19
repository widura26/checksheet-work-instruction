<?php

namespace App\Imports;

use App\Models\DaftarKodeMaterial;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ToolDibuangImport implements SkipsEmptyRows, ToCollection, WithHeadingRow
{
    public $errors = [];

    public $data = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $kodeMaterial = trim($row['kode_material'] ?? '');
            $detailKerusakan = trim($row['detail_kerusakan'] ?? '');

            $rowErrors = [];

            if (empty($kodeMaterial)) {
                $rowErrors[] = 'Kode material wajib diisi';
            }

            if (empty($detailKerusakan)) {
                $rowErrors[] = 'Detail kerusakan wajib diisi';
            }

            if (! empty($kodeMaterial)) {
                $material = DaftarKodeMaterial::where('kode_material', $kodeMaterial)->first();
                if (! $material) {
                    $rowErrors[] = "Kode material '{$kodeMaterial}' tidak ditemukan";
                } else {
                    if (empty($rowErrors)) {
                        $this->data[] = [
                            'kode_material' => $kodeMaterial,
                            'deskripsi' => $material->deskripsi,
                            'spesifikasi' => $material->spesifikasi ?? '-',
                            'detail_kerusakan' => $detailKerusakan,
                        ];
                    }
                }
            }

            if (! empty($rowErrors)) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => $kodeMaterial,
                    'errors' => $rowErrors,
                ];
            }
        }
    }
}
