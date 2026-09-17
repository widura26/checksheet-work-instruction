<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;

class MaterialPlanningImport implements ToCollection, WithBatchInserts, WithChunkReading, WithStartRow, SkipsEmptyRows, WithHeadingRow
{
    private array $errors = [];
    private int $importedCount = 0;

    public function collection(Collection $rows): void
    {
        $validRows = [];

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

            if (empty($row['wbs_elem']) && empty($row['material'])) {
                continue;
            }

            $validRows[] = [
                'id'                   => (string) Str::uuid(),
                'wbs_description'      => $row['wbs_description'] ?? '-',
                'pr_number'            => $row['purchreq'] ?? '-',
                'del_indicator'        => $row['del_indicator'] ?? '-',
                'pr_status'            => $row['pr_status'] ?? '-',
                'request_date'         => $row['reqdate'] ?? '-',
                'pr_delivered_date'    => $row['pr_deliv_dat'] ?? '-',
                'changed_on_date'      => $row['chngd_on'] ?? '-',
                'material_code'        => $row['material'] ?? '-',
                'material_description' => $row['mat_description'] ?? '-',
                'spesification'        => $row['spesifikasi'] ?? '-',
                'requested_quantity'   => $row['qty_requested'] ?? '-',
                'unit'                 => $row['un'] ?? '-',
                'ordered_quantity'     => $row['ordered'] ?? '-',
                'po_number'            => $row['po_number'] ?? '-',
                'po_date'              => $row['po_date'] ?? '-',
                'po_delivered_date'    => $row['po_deliv_dat'] ?? '-',
                'created_by'           => $row['created_by'] ?? '-',
                'request_by'           => $row['requested_by'] ?? '-',
                'wbs_elem'             => $row['wbs_elem'] ?? '-',
                'tracking_no'          => $row['trackingno'] ?? '-',
                'matl_group'           => $row['matl_group'] ?? '-',
                'rel'                  => $row['rel'] ?? '-',
                'item'                 => $row['item'] ?? '-',
                'pgr'                  => $row['pgr'] ?? '-',
                'plnt'                 => $row['plnt'] ?? '-',
                'rel_stat'             => $row['relstat'] ?? '-',
                'created_at'           => now(),
                'updated_at'           => now(),
            ];
        }

        if (empty($validRows)) {
            return;
        }

        Log::info('Valid rows count: ' . count($validRows));
        Log::info('First valid row:', $validRows[0]);

        try {
            DB::transaction(function () use ($validRows) {
                DB::table('material_plannings')->insert($validRows);
            });
            $this->importedCount += count($validRows);
        } catch (\Throwable $e) {
            $this->errors[] = "Gagal insert batch: " . $e->getMessage();
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}
