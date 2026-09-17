<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class MaterialPlanningImport implements ToCollection, WithHeadingRow, WithChunkReading, WithStartRow, SkipsEmptyRows, WithCustomCsvSettings
{
    private array $errors = [];
    private int $importedCount = 0;
    private array $headerMap = [];

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'input_encoding' => 'UTF-8',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function collection(Collection $rows): void
    {
        if ($this->headerMap === []) {
            $this->buildHeaderMap($rows->first());
        }

        $validRows = [];

        foreach ($rows as $index => $row) {
            $mapped = $this->mapRow($row);

            $isEmpty = true;
            foreach ($mapped as $value) {
                if (!empty(trim((string) $value))) {
                    $isEmpty = false;
                    break;
                }
            }

            if ($isEmpty) {
                continue;
            }

            if (empty($mapped['wbs_elem']) && empty($mapped['material_code'])) {
                continue;
            }

            $validRows[] = array_merge([
                'id'         => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ], $mapped);
        }

        if (empty($validRows)) {
            return;
        }

        try {
            DB::transaction(function () use ($validRows) {
                DB::table('material_plannings')->insert($validRows);
            });
            $this->importedCount += count($validRows);
        } catch (\Throwable $e) {
            $this->errors[] = "Gagal insert batch: " . $e->getMessage();
        }
    }

    private function buildHeaderMap($firstRow): void
    {
        $dbColumns = [
            'wbs_description', 'pr_number', 'del_indicator', 'pr_status',
            'request_date', 'pr_delivered_date', 'changed_on_date',
            'material_code', 'material_description', 'spesification',
            'requested_quantity', 'unit', 'ordered_quantity',
            'po_number', 'po_date', 'po_delivered_date',
            'created_by', 'request_by', 'wbs_elem', 'tracking_no',
            'matl_group', 'rel', 'item', 'pgr', 'plnt', 'rel_stat',
        ];

        $aliases = [
            'wbs_description'  => ['wbs description', 'wbs_description', 'wbsdescription'],
            'pr_number'        => ['purch.req.', 'purchreq', 'pr_number', 'prnumber', 'purch req'],
            'del_indicator'    => ['del. indicator', 'del_indicator', 'delindicator', 'del indicator'],
            'pr_status'        => ['pr status', 'pr_status', 'prstatus'],
            'request_date'     => ['req.date', 'reqdate', 'request_date', 'requestdate', 'req date'],
            'pr_delivered_date'=> ['pr deliv. dat', 'pr_deliv_dat', 'prdelivdat', 'pr deliv dat', 'pr deliv. dat'],
            'changed_on_date'  => ['chngd on', 'chngd_on', 'chngdon', 'chngd.on'],
            'material_code'    => ['material', 'material_code', 'materialcode'],
            'material_description' => ['mat. description', 'mat_description', 'matdescription', 'mat description'],
            'spesification'    => ['spesifikasi', 'spesification'],
            'requested_quantity'=> ['qty requested', 'qty_requested', 'qtyrequested', 'qty.requested'],
            'unit'             => ['un', 'unit'],
            'ordered_quantity' => ['ordered', 'ordered_quantity', 'orderedquantity'],
            'po_number'        => ['po number', 'po_number', 'ponumber', 'po.number'],
            'po_date'          => ['po date', 'po_date', 'podate', 'po.date'],
            'po_delivered_date'=> ['po deliv. dat', 'po_deliv_dat', 'podelivdat', 'po deliv dat', 'po deliv. dat'],
            'created_by'       => ['created by', 'created_by', 'createdby', 'created.by'],
            'request_by'       => ['requested by', 'requested_by', 'requestedby', 'requested.by'],
            'wbs_elem'         => ['wbs elem', 'wbs_elem', 'wbselem', 'wbs.elem'],
            'tracking_no'      => ['trackingno', 'tracking_no', 'tracking no'],
            'matl_group'       => ['matl group', 'matl_group', 'matlgroup', 'matl.group'],
            'rel'              => ['rel'],
            'item'             => ['item'],
            'pgr'              => ['pgr'],
            'plnt'             => ['plnt'],
            'rel_stat'         => ['relstat', 'rel_stat', 'rel stat', 'rel.stat'],
        ];

        $csvKeys = array_values($firstRow->toArray());
        $this->headerMap = array_fill_keys($csvKeys, null);

        foreach ($csvKeys as $csvKey) {
            $normalized = strtolower(trim($csvKey));

            foreach ($aliases as $dbCol => $aliasList) {
                if (in_array($normalized, $aliasList)) {
                    $this->headerMap[$csvKey] = $dbCol;
                    break;
                }
            }
        }
    }

    private function mapRow($row): array
    {
        $result = [];
        foreach ($row as $key => $value) {
            $dbCol = $this->headerMap[$key] ?? null;
            if ($dbCol) {
                $result[$dbCol] = $value ?? '-';
            }
        }
        return $result;
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

    public function chunkSize(): int
    {
        return 2000;
    }
}
