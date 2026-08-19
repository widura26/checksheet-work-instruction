<?php

namespace App\Exports;

use App\Models\UpdateStockTool;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UpdateStockToolExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = UpdateStockTool::with(['user:id,name', 'tool.posisiCT', 'project'])
            ->orderBy('update_stock_tools.created_at', 'desc');

        if (! empty($this->filters['kode_material'])) {
            $query->whereHas('tool', function ($q) {
                $q->where('kode_material', 'like', '%'.$this->filters['kode_material'].'%');
            });
        }

        if (! empty($this->filters['project_id'])) {
            $query->where('project_id', $this->filters['project_id']);
        }

        if (! empty($this->filters['start_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['start_date']);
        }

        if (! empty($this->filters['end_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['end_date']);
        }

        if (! empty($this->filters['vendor'])) {
            $query->where('vendor', $this->filters['vendor']);
        }

        if (! empty($this->filters['keterangan_vendor'])) {
            $query->where('keterangan_vendor', 'like', '%'.$this->filters['keterangan_vendor'].'%');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'User',
            'Kode Material',
            'Deskripsi',
            'Jumlah Ditambah',
            'Project',
            'Posisi',
            'Tipe',
            'Nomor Dokumen',
            'Tanggal',
            'Barang Datang Dari (Vendor)',
            'Keterangan Vendor',
        ];
    }

    public function map($row): array
    {
        return [
            $row->user->name ?? '-',
            $row->tool->kode_material ?? '-',
            $row->tool->deskripsi ?? '-',
            $row->qty_update,
            $row->project->nama_project ?? '-',
            $row->tool->posisiCT->nama_posisi_ct ?? '-',
            $row->type === 'input_material' ? 'Input Material' : ($row->type === 'tambah_stok' ? 'Tambah Stok' : $row->type),
            $row->nomor_dokumen ?? '-',
            $row->created_at->format('d-m-Y H:i:s'),
            $row->vendor ?? '-',
            $row->keterangan_vendor ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
