<?php

namespace App\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SapDatasetImport implements ToCollection, WithBatchInserts, WithChunkReading, WithStartRow, SkipsEmptyRows, WithHeadingRow,ShouldQueue
{
    public int $timeout = 600;
    public int $tries = 3;

    protected string $importId;

    public function __construct(string $importId)
    {
        $this->importId = $importId;
    }

    public function collection($rows)
    {
        $allData = [];

        foreach ($rows as $row) {
            $mapped = $this->mapRow($row);

            $filledCols = 0;
            foreach ($mapped as $v) {
                if (!empty(trim((string) $v))) {
                    $filledCols++;
                }
            }
            if ($filledCols <= 1) {
                continue;
            }

            $allData[] = [
                'wbs_elem'            => $mapped['wbs_elem'],
                'wbs_description'     => $mapped['wbs_description'],
                'pr_number'           => $mapped['pr_number'],
                'del_indicator'       => $mapped['del_indicator'],
                'pr_status'           => $mapped['pr_status'],
                'request_date'        => $mapped['request_date'],
                'pr_delivered_date'   => $mapped['pr_delivered_date'],
                'changed_on_date'     => $mapped['changed_on_date'],
                'material_code'       => $mapped['material_code'],
                'material_description'=> $mapped['material_description'],
                'spesification'       => $mapped['spesification'],
                'requested_quantity'  => $mapped['requested_quantity'],
                'unit'                => $mapped['unit'],
                'ordered_quantity'    => $mapped['ordered_quantity'],
                'po_number'           => $mapped['po_number'],
                'po_date'             => $mapped['po_date'],
                'po_delivered_date'   => $mapped['po_delivered_date'],
                'created_by'          => $mapped['created_by'],
                'request_by'          => $mapped['request_by'],
                'tracking_no'         => $mapped['tracking_no'],
                'matl_group'          => $mapped['matl_group'],
                'rel'                 => $mapped['rel'],
                'item'                => $mapped['item'],
                'pgr'                 => $mapped['pgr'],
                'plnt'                => $mapped['plnt'],
                'rel_stat'            => $mapped['rel_stat'],
                'status'              => (empty($mapped['wbs_elem']) && empty($mapped['material_code'])) ? 'invalid' : 'valid',
                'id'                  => (string) Str::uuid(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }
        DB::table('sap_datasets')->insert($allData);
    }

    private function mapRow($row): array
    {
        return [
            'wbs_elem'            => $row['wbs_elem'] ?? $row['wbs elem'] ?? null,
            'wbs_description'     => $row['wbs_description'] ?? $row['wbs description'] ?? null,
            'pr_number'           => $row['pr_number'] ?? $row['purchreq'] ?? $row['purch_req'] ?? null,
            'del_indicator'       => $row['del_indicator'] ?? $row['del_indicator'] ?? null,
            'pr_status'           => $row['pr_status'] ?? $row['pr status'] ?? null,
            'request_date'        => $row['request_date'] ?? $row['reqdate'] ?? null,
            'pr_delivered_date'   => $row['pr_delivered_date'] ?? $row['pr_deliv_dat'] ?? null,
            'changed_on_date'     => $row['changed_on_date'] ?? $row['chngd_on'] ?? null,
            'material_code'       => $row['material_code'] ?? $row['material'] ?? null,
            'material_description'=> $row['material_description'] ?? $row['mat_description'] ?? null,
            'spesification'       => $row['spesification'] ?? $row['spesifikasi'] ?? null,
            'requested_quantity'  => $row['requested_quantity'] ?? $row['qty_requested'] ?? null,
            'unit'                => $row['unit'] ?? $row['un'] ?? null,
            'ordered_quantity'    => $row['ordered_quantity'] ?? $row['ordered'] ?? null,
            'po_number'           => $row['po_number'] ?? $row['po number'] ?? null,
            'po_date'             => $row['po_date'] ?? $row['po date'] ?? null,
            'po_delivered_date'   => $row['po_delivered_date'] ?? $row['po_deliv_dat'] ?? null,
            'created_by'          => $row['created_by'] ?? $row['created by'] ?? null,
            'request_by'          => $row['request_by'] ?? $row['requested_by'] ?? null,
            'tracking_no'         => $row['tracking_no'] ?? $row['trackingno'] ?? null,
            'matl_group'          => $row['matl_group'] ?? $row['matl group'] ?? null,
            'rel'                 => $row['rel'] ?? null,
            'item'                => $row['item'] ?? null,
            'pgr'                 => $row['pgr'] ?? null,
            'plnt'                => $row['plnt'] ?? null,
            'rel_stat'            => $row['rel_stat'] ?? $row['relstat'] ?? null,
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 2000;
    }

    public function batchSize(): int
    {
        return 1000; // insert per 1k
    }

    public function failed(\Throwable $e): void
    {
        \Illuminate\Support\Facades\Log::error("Import {$this->importId} failed: {$e->getMessage()}");
    }
}
