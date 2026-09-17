<?php

namespace App\Http\Controllers;

use App\Imports\MaterialPlanningImport;
use App\Models\MaterialPlanning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class MaterialPlanningController extends Controller
{
    public function materialPlanning()
    {
        return view('material-planning.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|extensions:xlsx,xls,csv',
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.extensions' => 'File harus berformat xlsx, xls, atau csv.',
        ]);
        set_time_limit(300);
        DB::disableQueryLog();

        try {
            $import = new MaterialPlanningImport();
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();
            $importedCount = $import->getImportedCount();

            if (!empty($errors)) {
                return response()->json([
                    'success' => true,
                    'message' => "Import selesai. {$importedCount} baris berhasil diimpor, dengan beberapa error.",
                    'imported_count' => $importedCount,
                    'errors' => $errors,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$importedCount} baris data Purchase Requisition.",
                'imported_count' => $importedCount,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error importing material planning: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(){
        MaterialPlanning::truncate();
        return redirect()->back();
    }
}
