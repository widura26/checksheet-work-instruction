<?php

namespace App\Exports;

use App\Models\MaterialTransferPosting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialTransferPostingExport implements FromCollection, WithHeadings, WithMapping
{
    private $startDate;
    private $endDate;
    private $fromProjectId;
    private $toProjectId;
    private $status;
    private $kodeMaterial;
    public function __construct(
        $startDate,
        $endDate,
        $fromProjectId,
        $toProjectId,
        $status,
        $kodeMaterial
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->fromProjectId = $fromProjectId;
        $this->toProjectId = $toProjectId;
        $this->status = $status;
        $this->kodeMaterial = $kodeMaterial;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = MaterialTransferPosting::with('fromProject', 'toProject', 'createdBy', 'items.fromTool')
            ->latest();
        if ($this->startDate) {
            $query->whereDate('transfer_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('transfer_date', '<=', $this->endDate);
        }

        if ($this->fromProjectId) {
            $query->where('from_project_id', $this->fromProjectId);
        }

        if ($this->toProjectId) {
            $query->where('to_project_id', $this->toProjectId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->kodeMaterial) {
            $query->whereHas('items.fromTool', function ($query) {
                $query->where('tools.kode_material', $this->kodeMaterial);
            });
        }
        return $query->get()->flatMap(function ($row) {
            return $row->items->map(function ($item) use ($row) {
                $no = 0;
                return [
                    'no' => ++$no,
                    'id' => $row->id,
                    'from_project' => $row->fromProject->nama_project ?? '-',
                    'to_project' => $row->toProject->nama_project ?? '-',
                    'kode_material' => $item->fromTool->kode_material ?? '-',
                    'deskripsi' => $item->fromTool->deskripsi ?? '-',
                    'qty' => $item->qty,
                    'status' => $row->status,
                    'created_at' => $row->created_at,
                ];
            });
        })->values();
    }

    public function headings(): array
    {
        return [
            'No Transfer',
            'Dari Project',
            'Ke Project',
            'Kode Material',
            'Deskripsi',
            'Jumlah',
            'Status',
            'Tanggal Transfer',
        ];
    }

    public function map($row): array
    {
        return [
            $row['id'],
            $row['from_project'],
            $row['to_project'],
            $row['kode_material'],
            $row['deskripsi'],
            $row['qty'],
            $row['status'],
            $row['created_at'],
        ];
    }
}
