<?php

namespace App\Http\Controllers;

use App\Jobs\ImportBomDatasetJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BillOfMaterialController extends Controller
{
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|extensions:csv,xlsx,xls|max:20480',
            ], [
                'file.required' => 'Pilih file terlebih dahulu.',
                'file.extensions' => 'Format file harus .csv, .xlsx, atau .xls.',
                'file.max' => 'Ukuran file maksimal 20 MB.',
            ]);
        } catch (ValidationException $e) {
            return response()->json($e->errors(), 422);
        }

        $file = $request->file('file');
        $importId = (string) Str::uuid();
        $fileName = $importId . '.' . $file->getClientOriginalExtension();

        Storage::disk('local')->makeDirectory('bom-imports');
        $file->storeAs('bom-imports', $fileName);

        ImportBomDatasetJob::dispatch($importId, storage_path('app/bom-imports/' . $fileName));

        return response()->json([
            'success' => true,
            'import_id' => $importId,
            'message' => 'Import diproses di background...',
        ]);
    }

    public function status(string $importId)
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

        if ($failedJobs > 0) {
            $status = 'failed';
        } elseif ($pendingJobs > 0) {
            $status = 'pending';
        } else {
            $status = 'completed';
        }

        return response()->json([
            'import_id' => $importId,
            'status' => $status,
            'imported' => $imported,
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
        ]);
    }
}
