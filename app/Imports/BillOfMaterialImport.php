<?php

namespace App\Imports;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BillOfMaterialImport implements ToCollection, WithChunkReading, WithStartRow, SkipsEmptyRows, WithHeadingRow, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;
    public int $tries = 3;

    protected string $importId;
    protected string $sheetConfig;
    protected string $filePath;

    public function __construct(string $importId, string $sheetConfig, string $filePath)
    {
        $this->importId = $importId;
        $this->sheetConfig = $sheetConfig;
        $this->filePath = $filePath;
    }

    public function handle(): void
    {
        $startTime = microtime(true);

        if (!file_exists($this->filePath)) {
            Log::error("Import {$this->importId}: File not found");
            return;
        }

        try {
            \Maatwebsite\Excel\Facades\Excel::import($this, $this->filePath);

            $elapsed = round(microtime(true) - $startTime, 2);
            Log::info("Import {$this->importId} completed in {$elapsed}s");
        } catch (\Throwable $e) {
            Log::error("Import {$this->importId} error: " . $e->getMessage());
        }
    }

    public function collection($rows)
    {
        $allData = [];
        $config = config("bom-sheets.sheets.{$this->sheetConfig}");

        foreach ($rows as $row) {
            $mapped = $this->mapRow($row, $config);

            // Skip empty rows
            $filledCols = 0;
            foreach ($mapped as $v) {
                if (!empty(trim((string) $v))) {
                    $filledCols++;
                }
            }
            if ($filledCols <= 1) {
                continue;
            }

            // Validate required fields
            $isInvalid = false;
            foreach ($config['required'] as $field) {
                if (empty($mapped[$field])) {
                    $isInvalid = true;
                    break;
                }
            }

            $allData[] = [
                'import_id' => $this->importId,
                'sheet_name' => $this->sheetConfig,
                'takt' => $mapped['takt'],
                'rev_bom' => $mapped['rev_bom'],
                'no' => $mapped['no'],
                'kode_material' => $mapped['kode_material'],
                'kode_material_stock' => $mapped['kode_material_stock'],
                'status' => $isInvalid ? 'invalid' : 'valid',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Manual chunk insert
        foreach (array_chunk($allData, 500) as $chunk) {
            DB::table('bill_of_material_dataset')->insert($chunk);
        }
    }

    private function mapRow($row, array $config): array
    {
        $result = [];
        foreach ($config['headers'] as $dbCol => $aliases) {
            foreach ($aliases as $alias) {
                $normalized = strtolower(trim($alias));
                foreach ($row as $key => $value) {
                    if (strtolower(trim($key)) === $normalized) {
                        $result[$dbCol] = $value;
                        break 2;
                    }
                }
            }
            $result[$dbCol] = $result[$dbCol] ?? null;
        }
        return $result;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 2000;
    }

    public function failed(\Throwable $e): void
    {
        Log::error("Import {$this->importId} failed: {$e->getMessage()}");
    }
}
