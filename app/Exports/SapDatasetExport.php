<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SapDatasetExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $rows, private array $headings)
    {
    }

    public function collection(): Collection
    {
        return $this->rows->map(function ($row) {
            $data = [];
            foreach ($this->headings as $column) {
                $data[] = data_get($row, $column);
            }
            return $data;
        });
    }

    public function headings(): array
    {
        return $this->headings;
    }
}
