<?php

namespace App\Imports;

use App\Models\MaterialPlanning;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class MaterialPlanningImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsEmptyRows
{
    private array $errors = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $isEmpty = true;
            foreach ($row as $value) {
                if (!empty(trim((string) $value))) {
                    $isEmpty = false;
                    break;
                }
            }

            if ($isEmpty) {
                continue;
            }

            try {
                MaterialPlanning::create([
                    'wbs_description'     => $row['wbs_description'] ?? null,
                    'pr_number'           => $row['purch.req.'] ?? null,
                    'del_indicator'       => $row['del.indicator'] ?? null,
                    'pr_status'           => $row['pr.status'] ?? null,
                    'request_date'        => $row['req.date'] ?? null,
                    'pr_delivered_date'   => $row['pr.deliv.dat'] ?? null,
                    'changed_on_date'     => $row['chngd.on'] ?? null,
                    'material_code'       => $row['material'] ?? null,
                    'material_description'=> $row['mat.description'] ?? null,
                    'spesification'       => $row['spesifikasi'] ?? null,
                    'requested_quantity'  => $row['qty.requested'] ?? null,
                    'unit'                => $row['un'] ?? null,
                    'ordered_quantity'    => $row['ordered'] ?? null,
                    'po_number'           => $row['po.number'] ?? null,
                    'po_date'             => $row['po.date'] ?? null,
                    'po_delivered_date'   => $row['po.deliv.dat'] ?? null,
                    'created_by'          => $row['created.by'] ?? null,
                    'request_by'          => $row['requested.by'] ?? null,
                    'wbs_elem'            => $row['wbs.elem'] ?? null,
                    'tracking_no'         => $row['trackingno'] ?? null,
                    'matl_group'          => $row['matl.group'] ?? null,
                    'rel'                 => $row['rel'] ?? null,
                    'item'                => $row['item'] ?? null,
                    'pgr'                 => $row['pgr'] ?? null,
                    'plnt'                => $row['plnt'] ?? null,
                    'rel_stat'            => $row['rel.stat'] ?? null,
                ]);
            } catch (\Throwable $e) {
                $this->errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
            }
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }
}
