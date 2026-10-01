<?php

namespace App\Imports;

use App\Support\BomColumnMapper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;

class BomDatasetImport implements ToCollection, SkipsEmptyRows
{
    protected string $importId;

    public function __construct(string $importId)
    {
        $this->importId = $importId;
    }

    public function collection($rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $headerCells = $rows->first()->toArray();
        $map = BomColumnMapper::map($headerCells);

        if (!in_array('kode_material', $map, true)) {
            throw new \RuntimeException('Header file tidak dikenali — kolom "Kode Material" tidak ditemukan');
        }

        $positionMap = [];
        foreach ($headerCells as $index => $headerCell) {
            $normalized = BomColumnMapper::normalize((string) $headerCell);
            if (isset($map[$normalized])) {
                $positionMap[$index] = $map[$normalized];
            }
        }

        $allData = [];

        foreach ($rows->slice(1) as $row) {
            $rowCells = $row->toArray();
            $mapped = [];
            foreach ($positionMap as $index => $dbColumn) {
                $value = $rowCells[$index] ?? null;
                $mapped[$dbColumn] = is_string($value) ? trim($value) : $value;
            }

            $filled = count(array_filter($mapped, static fn ($v) => $v !== null && $v !== ''));
            if ($filled <= 1) {
                continue;
            }

            $allData[] = [
                'sheet_name'                  => $mapped['sheet_name'] ?? null,
                'kode_material'               => $mapped['kode_material'] ?? null,
                'kode_material_delete'        => $mapped['kode_material_delete'] ?? null,
                'kode_material_stock'         => $mapped['kode_material_stock'] ?? null,
                'material_komponen'           => $mapped['material_komponen'] ?? null,
                'spesifikasi'                 => $mapped['spesifikasi'] ?? null,
                'qty_ts_series'               => $mapped['qty_ts_series'] ?? null,
                'uom_car'                     => $mapped['uom_car'] ?? null,
                'qty_total_series_allowance'  => $mapped['qty_total_series_allowance'] ?? null,
                'qty_pr_total'                => $mapped['qty_pr_total'] ?? null,
                'status'                      => empty($mapped['kode_material']) ? 'invalid' : 'valid',
                'id'                          => (string) Str::uuid(),
                'import_id'                   => $this->importId,
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ];
        }

        DB::transaction(function () use ($allData) {
            DB::table('bill_of_material_dataset')->delete();

            foreach (array_chunk($allData, 500) as $chunk) {
                DB::table('bill_of_material_dataset')->insert($chunk);
            }
        });
    }
}
