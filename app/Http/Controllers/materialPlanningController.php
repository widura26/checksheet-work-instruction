<?php

namespace App\Http\Controllers;

use App\Imports\MaterialPlanningImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class materialPlanningController extends Controller
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

        try {
            $import = new MaterialPlanningImport();
            Excel::import($import, $request->file('file'));

            $errors = $import->getErrors();

            if (!empty($errors)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Import selesai dengan beberapa error.',
                    'errors' => $errors,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Purchase Requisition berhasil diimpor.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error importing material planning: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data: ' . $e->getMessage(),
            ], 500);
        }
    }
}
