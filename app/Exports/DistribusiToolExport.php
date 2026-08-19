<?php

namespace App\Exports;

use App\Models\DaftarDistribusiTool;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DistribusiToolExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = DaftarDistribusiTool::with([
            'tool',
            'distribusiTool.user',
            'distribusiTool.departemen',
            'distribusiTool.bagian',
            'distribusiTool.unit',
            'distribusiTool.project',
        ])->select('daftar_distribusi_tools.*');

        if (!empty($this->filters['type'])) {
            $query->whereHas('distribusiTool', function ($q) {
                $q->where('type', $this->filters['type']);
            });
        }
        if (!empty($this->filters['kode_material'])) {
            $query->whereHas('tool', function ($q) {
                $q->where('kode_material', $this->filters['kode_material']);
            });
        }
        if (!empty($this->filters['kategori'])) {
            $query->whereHas('distribusiTool', function ($q) {
                $q->where('kategori', $this->filters['kategori']);
            });
        }

        if (!empty($this->filters['departemen_id'])) {
            $query->whereHas('distribusiTool', function ($q) {
                $q->where('departemen_id', $this->filters['departemen_id']);
            });
        }

        if (!empty($this->filters['bagian_id'])) {
            $query->whereHas('distribusiTool', function ($q) {
                $q->where('bagian_id', $this->filters['bagian_id']);
            });
        }

        if (!empty($this->filters['unit_id'])) {
            $query->whereHas('distribusiTool', function ($q) {
                $q->where('unit_id', $this->filters['unit_id']);
            });
        }

        if (!empty($this->filters['project_id'])) {
            $query->whereHas('distribusiTool', function ($q) {
                $q->where('project_id', $this->filters['project_id']);
            });
        }
        if (!empty($this->filters['start_date'])) {
            $startDate = Carbon::parse($this->filters['start_date'])->startOfDay();
            $query->whereHas('distribusiTool', function ($q) use ($startDate) {
                $q->where('tanggal_pinjam_diserahkan', '>=', $startDate);
            });
        }
        if (!empty($this->filters['end_date'])) {
            $endDate = Carbon::parse($this->filters['end_date'])->endOfDay();
            $query->whereHas('distribusiTool', function ($q) use ($endDate) {
                $q->where('tanggal_pinjam_diserahkan', '<=', $endDate);
            });
        }
        return $query;
    }

    public function headings(): array
    {
        return [
            'Tipe',
            'Kode Material',
            'Deskripsi',
            'Spesifikasi',
            'Satuan',
            'Jumlah',
            'Kategori',
            'Nama Penerima',
            'NIP',
            'WBS ID',
            'Project',
            'Departemen',
            'Bagian',
            'Unit',
            'Staff',
            'Tanggal Diserahkan',
            'Tanggal Kembali',
            'Status',
        ];
    }
    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_TEXT, // kolom NIP
        ];
    }
    public function map($row): array
    {
        return [
            ucfirst($row->distribusiTool->type ?? '-'),
            $row->tool->kode_material ?? '-',
            $row->tool->deskripsi ?? '-',
            $row->tool->spesifikasi ?? '-',
            $row->tool->satuan ?? '-',
            $row->jumlah_distribusi_formatted ?? '-',
            strtoupper($row->tool->jenis_material ?? '-'),
            $row->distribusiTool->nama_peminjam ?? '-',
            $row->distribusiTool->nip ?? '-',
            $row->distribusiTool->project->project_id ?? '-',
            $row->distribusiTool->project->nama_project ?? '-',
            $row->distribusiTool->departemen->nama_departemen ?? '-',
            $row->distribusiTool->bagian->nama_bagian ?? '-',
            $row->distribusiTool->unit->nama_unit ?? '-',
            $row->distribusiTool->user->name ?? '-',
            optional($row->distribusiTool->tanggal_pinjam_diserahkan)->format('d-m-Y H:i'),
            optional($row->distribusiTool->tanggal_kembali)->format('d-m-Y H:i') ?? '-',
            ucfirst(str_replace('_', ' ', $row->distribusiTool->status ?? '-')),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
