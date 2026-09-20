<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class ImportMaterialPlanningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public string $filePath,
        public string $importId
    ) {}

    public function handle(): void
    {
        $startTime = microtime(true);
        $tempFile = storage_path('app/imports/' . $this->filePath);

        if (!file_exists($tempFile)) {
            Log::error("Import {$this->importId}: File not found at {$tempFile}");
            return;
        }

        $content = file_get_contents($tempFile);
        $content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'ASCII, ISO-8859-1, Windows-1252, UTF-8', true));
        $tempFileConverted = tempnam(sys_get_temp_dir(), 'csv_');
        file_put_contents($tempFileConverted, $content);

        $handle = fopen($tempFileConverted, 'r');
        $firstLine = fgets($handle);
        rewind($handle);

        if (str_contains($firstLine, "\t")) {
            $delimiter = "\t";
        } elseif (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        } else {
            $delimiter = ',';
        }

        $header = fgetcsv($handle, 0, $delimiter);
        $headerMap = $this->buildHeaderMap($header);

        $totalImported = 0;
        $totalInvalid = 0;
        $totalShifted = 0;
        $errors = [];

        LazyCollection::make(function () use ($handle, $header, $delimiter, &$totalShifted) {
            $colCount = count($header);
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (empty($row) || !isset($row[0])) continue;
                if (count($row) === $colCount + 1 && trim($row[0]) === '') {
                    array_shift($row);
                    $totalShifted++;
                }
                yield $row;
            }
            fclose($handle);
        })
        ->each(function ($row) use ($headerMap, &$totalImported, &$totalInvalid, &$errors) {
            $mapped = $this->mapRow($row, $headerMap);

            $totalCols = count($mapped);
            $filledCols = 0;
            foreach ($mapped as $v) {
                if (!empty(trim((string) $v))) {
                    $filledCols++;
                }
            }

            if ($filledCols <= $totalCols / 2) {
                return;
            }

            $isInvalid = empty($mapped['wbs_elem']) && empty($mapped['material_code']);

            try {
                $prepared = $this->prepareRow($mapped, $isInvalid ? 'invalid' : 'valid');
                DB::table('material_plannings')->insert($prepared);
                if ($isInvalid) {
                    $totalInvalid++;
                } else {
                    $totalImported++;
                }
            } catch (\Throwable $e) {
                try {
                    $prepared = $this->prepareRow($mapped, 'invalid');
                    DB::table('material_plannings')->insert($prepared);
                    $totalInvalid++;
                } catch (\Throwable $e2) {
                    $errors[] = $e2->getMessage();
                    Log::error("Import {$this->importId} row error: " . $e2->getMessage());
                }
            }
        });

        @unlink($tempFileConverted);
        @unlink($tempFile);

        $elapsed = round(microtime(true) - $startTime, 2);
        $totalAll = $totalImported + $totalInvalid;

        Log::info("Import {$this->importId} completed: {$totalAll} rows ({$totalImported} valid, {$totalInvalid} invalid) in {$elapsed}s");
        if ($totalShifted > 0) {
            Log::info("Import {$this->importId}: {$totalShifted} rows shifted");
        }
        if (!empty($errors)) {
            Log::warning("Import {$this->importId}: " . count($errors) . " rows failed to insert");
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error("Import {$this->importId} failed: {$e->getMessage()}");

        $tempFile = storage_path('app/imports/' . $this->filePath);
        @unlink($tempFile);
    }

    private function buildHeaderMap(array $header): array
    {
        $aliases = [
            'wbs_description'   => ['wbs description', 'wbs_description', 'wbsdescription'],
            'pr_number'         => ['purch.req.', 'purchreq', 'pr_number', 'prnumber', 'purch req'],
            'del_indicator'     => ['del. indicator', 'del_indicator', 'delindicator', 'del indicator'],
            'pr_status'         => ['pr status', 'pr_status', 'prstatus'],
            'request_date'      => ['req.date', 'reqdate', 'request_date', 'requestdate', 'req date'],
            'pr_delivered_date' => ['pr deliv. dat', 'pr_deliv_dat', 'prdelivdat', 'pr deliv dat', 'pr deliv. dat'],
            'changed_on_date'   => ['chngd on', 'chngd_on', 'chngdon', 'chngd.on'],
            'material_code'     => ['material', 'material_code', 'materialcode'],
            'material_description' => ['mat. description', 'mat_description', 'matdescription', 'mat description'],
            'spesification'     => ['spesifikasi', 'spesification'],
            'requested_quantity'=> ['qty requested', 'qty_requested', 'qtyrequested', 'qty.requested'],
            'unit'              => ['un', 'unit'],
            'ordered_quantity'  => ['ordered', 'ordered_quantity', 'orderedquantity'],
            'po_number'         => ['po number', 'po_number', 'ponumber', 'po.number'],
            'po_date'           => ['po date', 'po_date', 'podate', 'po.date'],
            'po_delivered_date' => ['po deliv. dat', 'po_deliv_dat', 'podelivdat', 'po deliv dat', 'po deliv. dat'],
            'created_by'        => ['created by', 'created_by', 'createdby', 'created.by'],
            'request_by'        => ['requested by', 'requested_by', 'requestedby', 'requested.by'],
            'wbs_elem'          => ['wbs elem', 'wbs_elem', 'wbselem', 'wbs.elem', 'WBS Elem'],
            'tracking_no'       => ['trackingno', 'tracking_no', 'tracking no'],
            'matl_group'        => ['matl group', 'matl_group', 'matlgroup', 'matl.group'],
            'rel'               => ['rel'],
            'item'              => ['item'],
            'pgr'               => ['pgr'],
            'plnt'              => ['plnt'],
            'rel_stat'          => ['relstat', 'rel_stat', 'rel stat', 'rel.stat'],
        ];

        $map = [];
        foreach ($header as $index => $col) {
            $normalized = strtolower(trim($col));
            foreach ($aliases as $dbCol => $aliasList) {
                if (in_array($normalized, $aliasList)) {
                    $map[$index] = $dbCol;
                    break;
                }
            }
        }
        return $map;
    }

    private function mapRow(array $row, array $headerMap): array
    {
        $result = [];
        foreach ($headerMap as $index => $dbCol) {
            $result[$dbCol] = $row[$index] ?? '-';
        }
        return $result;
    }

    private function prepareRow(array $mapped, string $status): array
    {
        $mapped['id'] = (string) Str::uuid();
        $mapped['created_at'] = now();
        $mapped['updated_at'] = now();
        $mapped['status'] = $status;

        if ($status === 'invalid') {
            foreach ($mapped as $key => $val) {
                if (is_string($val) && $key !== 'id') {
                    $mapped[$key] = mb_substr($val, 0, 255);
                }
            }
        }

        return $mapped;
    }
}
