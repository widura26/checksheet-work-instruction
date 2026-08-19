<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AwalanToolsValidationImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param Collection $collection
     */
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // karena heading
            $projectId = trim($row['project_id']);
            if (!isset($row['kode_material']) || trim($row['kode_material']) === '') {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => null,
                    'error' => 'Kode material wajib diisi',
                ];
                continue;
            }

            if (!\App\Models\DaftarKodeMaterial::where(
                'kode_material',
                trim($row['kode_material'])
            )->exists()) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => $row['kode_material'],
                    'error' => 'Kode material tidak ditemukan',
                ];
            }

            if (!in_array(trim($row['posisi_ct'] ?? ''), ['FAB', 'FIN', 'UU'])) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => $row['kode_material'] ?? null,
                    'error' => 'Posisi CT harus FAB,FIN, Atau UU',
                ];
            }

            if (!DB::table('projects')
                ->where('project_id', $projectId ?? '')
                ->exists()) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'kode_material' => $row['kode_material'] ?? null,
                    'error' => 'Project tidak ditemukan',
                ];
            }
        }
    }
}
