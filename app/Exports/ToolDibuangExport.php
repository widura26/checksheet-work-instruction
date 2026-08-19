<?php

namespace App\Exports;

use App\Models\DaftarToolDibuang;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ToolDibuangExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = DaftarToolDibuang::with('toolDibuang')->select('daftar_tool_dibuangs.*');

        if (! empty($this->filters['from_date'])) {
            $fromDate = Carbon::parse($this->filters['from_date'])->startOfDay();
            $query->whereHas('toolDibuang', function ($q) use ($fromDate) {
                $q->where('tanggal_dibuang', '>=', $fromDate);
            });
        }

        if (! empty($this->filters['to_date'])) {
            $toDate = Carbon::parse($this->filters['to_date'])->endOfDay();
            $query->whereHas('toolDibuang', function ($q) use ($toDate) {
                $q->where('tanggal_dibuang', '<=', $toDate);
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Dibuang',
            'Nomor BS',
            'Kode Material',
            'Deskripsi',
            'Spesifikasi',
            'Detail Kerusakan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            Carbon::parse($row->toolDibuang->tanggal_dibuang)->format('d-m-Y H:i'),
            $row->toolDibuang->nomor_bs,
            $row->kode_material,
            $row->deskripsi,
            $row->spesifikasi,
            $row->detail_kerusakan,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
