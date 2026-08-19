<?php

namespace App\Http\Controllers;

use App\Imports\AwalanToolsImport;
use App\Imports\AwalanToolsValidationImport;
use App\Imports\KodeMaterialImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DaftarKodeMaterialController extends Controller
{
    public function index()
    {
        return view('pages.daftar-kode-material.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }
        $query = DB::table('daftar_kode_materials')->select(['id', 'kode_material', 'satuan', 'deskripsi', 'spesifikasi']);
        if ($request->filled('kode_material')) {
            $query->where('kode_material', '=', $request->kode_material);
        }
        if ($request->filled('deskripsi')) {
            $query->where('deskripsi', 'LIKE', '%' . $request->deskripsi . '%');
        }
        if ($request->filled('spesifikasi')) {
            $query->where('spesifikasi', 'LIKE', '%' . $request->spesifikasi . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function findByKode(Request $request)
    {
        $material = \App\Models\DaftarKodeMaterial::where('kode_material', $request->kode_material)->first();

        if ($material) {
            return response()->json([
                'success' => true,
                'data' => $material
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Material tidak ditemukan'
        ]);
    }

    public function import(Request $request)
    {
        if (! Auth::user()->hasRole(['admin'])) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx,csv',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'File harus berformat XLS atau XLSX.');
        }

        try {
            $file = $request->file('file');
            Excel::import(new KodeMaterialImport, $file);

            return redirect()->back()->with('success', 'Data Akan Di Input Secara Bertahap Di Latar Belakang.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    public function importMaterial(Request $request)
    {
        if (! Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $file = $request->file('file');
        $validator = new AwalanToolsValidationImport();
        Excel::import($validator, $file);

        if (!empty($validator->errors)) {
            return back()->with([
                'import_errors' => $validator->errors,
                'message' => 'Import dibatalkan, periksa error berikut.',
            ]);
        }

        $import = new AwalanToolsImport();
        Excel::import(new AwalanToolsImport, $file);

        return redirect()->back()->with('success', 'Berhasil mengimport data.');
    }
}
