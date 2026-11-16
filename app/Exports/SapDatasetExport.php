<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SapDatasetExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'NO',
            'Group',
            'Tindak Lanjut',
            'Deskripsi Material',
            'Kebutuhan',
            'Qty PR (BOM)',
            'Kode Material',
            'Trainset',
            'Status PR',
            'Proyek',
        ];
    }

    public function map($row): array
    {
        return [
            $row['no'] ?? '',
            $row['group'] ?? '',
            $row['tindak_lanjut'] ?? '',
            $row['deskripsi'] ?? '',
            $row['kebutuhan'] ?? 0,
            $row['qty_pr'] ?? 0,
            $row['kode_material'] ?? '',
            $row['trainset'] ?? '',
            $row['status_pr'] ?? '',
            $row['proyek'] ?? '',
        ];
    }
}
