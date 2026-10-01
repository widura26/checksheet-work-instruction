<?php

namespace App\Http\Controllers;

use App\Models\SapDataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use App\Imports\SapDatasetImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class SapDatasetController extends Controller
{
    public function index(){
        $prtotals = SapDataset::all();
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

                    DB::table('sap_datasets')->insert($mapped);
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
                        DB::table('sap_datasets')->insert($mapped);
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

    public function importQueue(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:xlsx,xls,csv',
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.extensions' => 'File harus berformat xlsx, xls, atau csv.',
        ]);

        $file = $request->file('file');
        $importId = (string) Str::uuid();
        $extension = $file->getClientOriginalExtension();
        $fileName = $importId . '.' . $extension;

        Storage::disk('local')->makeDirectory('imports');
        $file->storeAs('imports', $fileName);

        $tempFile = storage_path('app/imports/' . $fileName);
        Excel::import(new SapDatasetImport($importId), $tempFile);

        return response()->json([
            'success' => true,
            'import_id' => $importId,
            'message' => 'Import diproses di background. Gunakan import_id untuk cek status.',
        ]);
    }

    public function importStatus(string $importId)
    {
        if (!preg_match('/^[0-9a-f-]+$/i', $importId)) {
            return response()->json([
                'import_id' => $importId,
                'status' => 'invalid',
                'message' => 'Format import ID tidak valid.',
            ], 422);
        }

        // NOTE: LIKE on serialized payload is fragile but intentional per spec design.
        // The importId is a UUID embedded in the serialized job payload.
        $jobExists = DB::table('jobs')
            ->where('payload', 'LIKE', '%'.$importId.'%')
            ->exists();

        $failedJob = DB::table('failed_jobs')
            ->where('payload', 'LIKE', '%'.$importId.'%')
            ->first();

        if ($failedJob) {
            return response()->json([
                'import_id' => $importId,
                'status' => 'failed',
                'error' => $failedJob->exception,
                'failed_at' => $failedJob->failed_at,
            ]);
        }

        if ($jobExists) {
            return response()->json([
                'import_id' => $importId,
                'status' => 'processing',
                'message' => 'Job masih diproses oleh queue worker.',
            ]);
        }

        return response()->json([
            'import_id' => $importId,
            'status' => 'completed',
            'message' => 'Import telah selesai. Cek log untuk detail.',
        ]);
    }

    private function buildHeaderMap(array $header): array {
        $aliases = [
            'wbs_elem'          => ['wbs elem', 'wbs_elem', 'wbselem', 'wbs.elem'],
            'wbs_description'   => ['wbs description', 'wbs_description', 'wbsdescription'],
            'pr_number'         => ['purch.req.', 'purchreq', 'pr_number', 'prnumber', 'purch req', 'purch. req.'],
            'del_indicator'     => ['del. indicator', 'del_indicator', 'delindicator', 'del indicator'],
            'pr_status'         => ['pr status', 'pr_status', 'prstatus'],
            'request_date'      => ['req.date', 'reqdate', 'request_date', 'requestdate', 'req date'],
            'pr_delivered_date' => ['pr deliv. dat', 'pr_deliv_dat', 'prdelivdat', 'pr deliv dat', 'pr deliv. dat'],
            'changed_on_date'   => ['chngd on', 'chngd_on', 'chngdon', 'chngd.on'],
            'material_code'     => ['material', 'material_code', 'materialcode'],
            'material_description' => ['mat. description', 'mat_description', 'matdescription', 'mat description'],
            'spesification'     => ['spesifikasi', 'spesification'],
            'requested_quantity'=> ['qty requested', 'qty_requested', 'qtyrequested', 'qty.requested', 'qty. requested'],
            'unit'              => ['un', 'unit'],
            'ordered_quantity'  => ['ordered', 'ordered_quantity', 'orderedquantity'],
            'po_number'         => ['po number', 'po_number', 'ponumber', 'po.number', 'po. number'],
            'po_date'           => ['po date', 'po_date', 'podate', 'po.date', 'po. date'],
            'po_delivered_date' => ['po deliv. dat', 'po_deliv_dat', 'podelivdat', 'po deliv dat', 'po deliv. dat'],
            'created_by'        => ['created by', 'created_by', 'createdby', 'created.by'],
            'request_by'        => ['requested by', 'requested_by', 'requestedby', 'requested.by', 'request by'],
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
        SapDataset::truncate();
        return redirect()->back();
    }

    public function deviation(Request $request){
        if (! $request->ajax()) {
            abort(404);
        }

        $query = SapDataset::select(
            'pr_number',
            DB::raw('MIN(wbs_elem) as wbs_elem'),
            DB::raw('MIN(wbs_description) as wbs_description'),
            DB::raw('MIN(material_code) as material_code'),
            DB::raw('MIN(material_description) as material_description'),
            DB::raw('MIN(unit) as unit'),
            DB::raw('MIN(po_number) as po_number'),
            DB::raw('MIN(created_by) as created_by'),
            DB::raw('COUNT(*) as total_records'),
            DB::raw('SUM(CAST(REPLACE(requested_quantity, ",", ".") AS DECIMAL(15,2))) as total_requested'),
            DB::raw('SUM(CAST(REPLACE(ordered_quantity, ",", ".") AS DECIMAL(15,2))) as total_ordered'),
            DB::raw('SUM(CAST(REPLACE(requested_quantity, ",", ".") AS DECIMAL(15,2))) - SUM(CAST(REPLACE(ordered_quantity, ",", ".") AS DECIMAL(15,2))) as selisih')
        )
        ->groupBy('pr_number')
        ->havingRaw('SUM(CAST(REPLACE(requested_quantity, ",", ".") AS DECIMAL(15,2))) != SUM(CAST(REPLACE(ordered_quantity, ",", ".") AS DECIMAL(15,2)))');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pr_number', 'LIKE', "%{$search}%")
                    ->orWhere('wbs_description', 'LIKE', "%{$search}%")
                    ->orWhere('material_description', 'LIKE', "%{$search}%")
                    ->orWhereIn('pr_number', function ($sub) use ($search) {
                        $sub->select('pr_number')
                            ->from('sap_datasets')
                            ->where('material_code', 'LIKE', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $data = $query->orderBy('pr_number')->paginate($perPage);

        $data->setCollection(
            $data->getCollection()->map(function ($item) {
                return [
                    'pr_number'            => $item->pr_number,
                    'wbs_elem'             => $item->wbs_elem,
                    'wbs_description'      => $item->wbs_description,
                    'material_code'        => $item->material_code,
                    'material_description' => $item->material_description,
                    'unit'                 => $item->unit,
                    'po_number'            => $item->po_number,
                    'created_by'           => $item->created_by,
                    'total_records'        => $item->total_records,
                    'total_requested'      => number_format($item->total_requested, 2, ',', '.'),
                    'total_ordered'        => number_format($item->total_ordered, 2, ',', '.'),
                    'selisih'              => number_format($item->selisih, 2, ',', '.'),
                ];
            })
        );

        return response()->json($data);
    }
}
