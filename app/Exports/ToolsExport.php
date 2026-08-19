<?php

namespace App\Exports;

use App\Models\ToolProjectStock;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ToolsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters, string $jenisMaterial = 'tool')
    {
        $this->filters = $filters;
        $this->jenisMaterial = $jenisMaterial;
    }

    public function query()
    {
        $query = ToolProjectStock::whereHas('tool', function ($q) {
            $q->where('jenis_material', $this->jenisMaterial);
        })->whereHas('project', function ($q) {
            $q->whereNull('closed_at');
        })->with(['tool', 'project', 'posisiCT']);

        if (!empty($this->filters['kode_material'])) {
            $query->whereHas('tool', function ($q) {
                $q->where('kode_material', 'like', '%' . $this->filters['kode_material'] . '%');
            });
        }

        if (!empty($this->filters['deskripsi'])) {
            $query->whereHas('tool', function ($q) {
                $q->where('deskripsi', 'like', '%' . $this->filters['deskripsi'] . '%');
            });
        }

        if (!empty($this->filters['spesifikasi'])) {
            $query->whereHas('tool', function ($q) {
                $q->where('spesifikasi', 'like', '%' . $this->filters['spesifikasi'] . '%');
            });
        }

        if (!empty($this->filters['posisi_ct_id'])) {
            $query->where('posisi_ct_id', $this->filters['posisi_ct_id']);
        }

        if (!empty($this->filters['project_id'])) {
            $query->where('project_id', $this->filters['project_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Material',
            'Deskripsi',
            'Spesifikasi',
            'Satuan',
            'Posisi CT',
            'Project',
            'Stok',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->tool->kode_material ?? '-',
            $row->tool->deskripsi ?? '-',
            $row->tool->spesifikasi ?? '-',
            $row->tool->satuan ?? '-',
            optional($row->posisiCT)->nama_posisi_ct ?? '',
            $row->project->nama_project ?? '-',
            $row->qty_formatted,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
