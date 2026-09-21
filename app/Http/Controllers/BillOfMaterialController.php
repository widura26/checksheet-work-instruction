<?php

namespace App\Http\Controllers;

use App\Imports\BillOfMaterialImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BillOfMaterialController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file|extensions:xlsx,xls']);

        $file = $request->file('file');
        $importId = (string) Str::uuid();
        $extension = $file->getClientOriginalExtension();
        $fileName = $importId . '.' . $extension;

        Storage::disk('local')->makeDirectory('bom-imports');
        $file->storeAs('bom-imports', $fileName);

        // Read sheet names only (lightweight, no full load)
        $filePath = storage_path("app/bom-imports/{$fileName}");
        $reader = IOFactory::createReaderForFile($filePath);
        $sheets = $reader->listWorksheetNames($filePath);

        return response()->json([
            'success' => true,
            'import_id' => $importId,
            'file_name' => $fileName,
            'sheets' => $sheets,
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'import_id' => 'required|uuid',
            'file_name' => 'required|string',
            'sheets' => 'required|array',
        ]);

        $tempFile = storage_path("app/bom-imports/{$request->file_name}");
        if (!str_starts_with(realpath($tempFile) ?: $tempFile, storage_path("app/bom-imports"))) {
            abort(400, 'Invalid file path');
        }

        $reader = IOFactory::createReaderForFile($tempFile);
        $spreadsheet = $reader->load($tempFile);

        $preview = [];
        foreach ($request->sheets as $sheetName) {
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if ($sheet) {
                $rows = $sheet->toArray(null, 0, 6); // Header + 5 rows
                $preview[$sheetName] = $rows;
            }
        }

        return response()->json(['preview' => $preview]);
    }

    public function importSheets(Request $request)
    {
        $request->validate([
            'import_id' => 'required|uuid',
            'file_name' => 'required|string',
            'sheets' => 'required|array',
        ]);

        $tempFile = storage_path("app/bom-imports/{$request->file_name}");
        if (!str_starts_with(realpath($tempFile) ?: $tempFile, storage_path("app/bom-imports"))) {
            abort(400, 'Invalid file path');
        }

        $imported = [];
        $skipped = [];
        foreach ($request->sheets as $sheetName) {
            $config = $this->findSheetConfig($sheetName);

            if ($config) {
                BillOfMaterialImport::dispatch($request->import_id, $config, $tempFile);
                $imported[] = $sheetName;
            } else {
                $skipped[] = $sheetName;
            }
        }

        return response()->json([
            'success' => true,
            'import_id' => $request->import_id,
            'message' => 'Import diproses di background',
            'imported' => $imported,
            'skipped' => $skipped,
        ]);
    }

    public function importStatus(string $importId)
    {
        $pendingJobs = DB::table('jobs')
            ->where('payload', 'LIKE', "%{$importId}%")
            ->count();

        $failedJobs = DB::table('failed_jobs')
            ->where('payload', 'LIKE', "%{$importId}%")
            ->count();

        $imported = DB::table('bill_of_material_dataset')
            ->where('import_id', $importId)
            ->count();

        $status = 'pending';
        if ($pendingJobs === 0 && $failedJobs === 0 && $imported > 0) {
            $status = 'completed';
        } elseif ($failedJobs > 0) {
            $status = 'failed';
        }

        return response()->json([
            'import_id' => $importId,
            'status' => $status,
            'imported' => $imported,
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
        ]);
    }

    private function findSheetConfig(string $sheetName): ?string
    {
        $configs = config('bom-sheets.sheets');
        foreach ($configs as $key => $config) {
            if (strtolower($sheetName) === strtolower($key) ||
                strtolower($sheetName) === strtolower($config['label'])) {
                return $key;
            }
        }
        return null;
    }
}
