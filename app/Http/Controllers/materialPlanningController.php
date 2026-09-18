<?php

namespace App\Http\Controllers;

use App\Models\MaterialPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class MaterialPlanningController extends Controller
{
    public function materialPlanning(){
        $prtotals = MaterialPlanning::all();
        return view('material-planning.index', compact('prtotals'));
    }

    public function import(Request $request){
        $request->validate([
            'file' => 'required|file|extensions:xlsx,xls,csv',
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.extensions' => 'File harus berformat xlsx, xls, atau csv.',
        ]);

        set_time_limit(300);
        DB::disableQueryLog();
        $startTime = microtime(true);

        $file = $request->file('file');
        $content = file_get_contents($file->getRealPath());
        $content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'ASCII, ISO-8859-1, Windows-1252, UTF-8', true));
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_');
        file_put_contents($tempFile, $content);
        $handle = fopen($tempFile, 'r');

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

        Log::info('Delimiter: ' . ($delimiter === "\t" ? "TAB" : "COMMA"));
        Log::info('Header count: ' . count($header));
        Log::info('Header:', $header);

        $headerMap = $this->buildHeaderMap($header);

        $totalImported = 0;
        $totalInvalid = 0;
        $totalShifted = 0;
        $errors = [];

        try {
            LazyCollection::make(function () use ($handle, $header, $delimiter, &$totalShifted) {
                $colCount = count($header);
                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if (empty($row) || !isset($row[0])) continue;
                    if (count($row) === $colCount + 1 && trim($row[0]) === '') {
                        Log::info('Row shifted, fixed: ' . $row[1]);
                        array_shift($row);
                        $totalShifted++;
                    }
                    yield $row;
                }
                fclose($handle);
            })
            ->each(function ($row) use ($headerMap, &$totalImported, &$totalSkipped, &$totalInvalid, &$errors) {
                $mapped = $this->mapRow($row, $headerMap);

                $isEmpty = true;
                foreach ($mapped as $v) {
                    if (!empty(trim((string) $v))) {
                        $isEmpty = false;
                        break;
                    }
                }

                $isInvalid = $isEmpty || (empty($mapped['wbs_elem']) && empty($mapped['material_code']));

                try {
                    $mapped['id'] = (string) Str::uuid();
                    $mapped['created_at'] = now();
                    $mapped['updated_at'] = now();
                    $mapped['status'] = $isInvalid ? 'invalid' : 'valid';

                    if ($isInvalid) {
                        foreach ($mapped as $key => $val) {
                            if (is_string($val) && $key !== 'id') {
                                $mapped[$key] = mb_substr($val, 0, 255);
                            }
                        }
                    }

                    DB::table('material_plannings')->insert($mapped);
                    if ($isInvalid) {
                        $totalInvalid++;
                    } else {
                        $totalImported++;
                    }
                } catch (\Throwable $e) {
                    try {
                        $mapped['id'] = (string) Str::uuid();
                        $mapped['created_at'] = now();
                        $mapped['updated_at'] = now();
                        $mapped['status'] = 'invalid';
                        foreach ($mapped as $key => $val) {
                            if (is_string($val) && $key !== 'id') {
                                $mapped[$key] = mb_substr($val, 0, 255);
                            }
                        }
                        DB::table('material_plannings')->insert($mapped);
                        $totalInvalid++;
                    } catch (\Throwable $e2) {
                        $errors[] = $e2->getMessage();
                        Log::error('Import row error: ' . $e2->getMessage());
                    }
                }
            });

            $elapsed = round(microtime(true) - $startTime, 2);
            $totalAll = $totalImported + $totalInvalid;
            $msg = "Berhasil mengimpor {$totalAll} baris data ({$totalImported} valid, {$totalInvalid} invalid). Waktu: {$elapsed} detik.";
            if ($totalShifted > 0) {
                $msg .= " {$totalShifted} baris di-shift (koreksi posisi kolom).";
            }
            if (!empty($errors)) {
                $msg .= " (" . count($errors) . " baris gagal insert)";
            }

            @unlink($tempFile);
            return response()->json([
                'success' => true,
                'message' => $msg,
                'imported_count' => $totalAll,
                'valid_count' => $totalImported,
                'invalid_count' => $totalInvalid,
                'elapsed' => $elapsed,
                'errors' => array_slice($errors, 0, 5),
            ]);
        } catch (\Throwable $e) {
            @unlink($tempFile);
            Log::error('Error importing material planning: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function buildHeaderMap(array $header): array {
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

    private function mapRow(array $row, array $headerMap): array {
        $result = [];
        foreach ($headerMap as $index => $dbCol) {
            $result[$dbCol] = $row[$index] ?? '-';
        }
        return $result;
    }

    public function delete(){
        MaterialPlanning::truncate();
        return redirect()->back();
    }
}
