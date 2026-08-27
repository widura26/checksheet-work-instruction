<?php

namespace App\Http\Controllers;

use App\Http\Requests\DaftarKaryawanRequest;
use App\Imports\KaryawanImport;
use App\Models\Departemen;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class DaftarKaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departemens = Departemen::all();

        return view('pages.karyawan.index', compact('departemens'));
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Karyawan::with(['departemen', 'bagian', 'unit']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%")
                    ->orWhereHas('departemen', function ($q) use ($search) {
                        $q->where('nama_departemen', 'like', "%{$search}%");
                    })
                    ->orWhereHas('bagian', function ($q) use ($search) {
                        $q->where('nama_bagian', 'like', "%{$search}%");
                    })
                    ->orWhereHas('unit', function ($q) use ($search) {
                        $q->where('nama_unit', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status === 'kosong') {
            $query->whereNull('departemen_id')
                ->orWhereNull('bagian_id')
                ->orWhereNull('unit_id');
        }

        $perPage = (int) $request->get('per_page', 10);
        $karyawans = $query->paginate($perPage);

        $karyawans->setCollection($karyawans->getCollection()->map(function ($karyawan) {
            return [
                'nip' => $karyawan->nip,
                'nama' => $karyawan->nama,
                'departemen' => $karyawan->departemen->nama_departemen ?? '-',
                'bagian' => $karyawan->bagian->nama_bagian ?? '-',
                'unit' => $karyawan->unit->nama_unit ?? '-',
                'jabatan' => $karyawan->jabatan,
            ];
        }));

        return response()->json($karyawans);
    }

    public function show(Karyawan $karyawan)
    {
        return response()->json($karyawan);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DaftarKaryawanRequest $request)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403);
        }
        $validated = $request->validated();
        try {
            Karyawan::create($validated);
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil ditambahkan.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error creating karyawan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data karyawan.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DaftarKaryawanRequest $request, Karyawan $karyawan)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403);
        }
        $validated = $request->validated();
        try {
            $karyawan->update($validated);
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error updating karyawan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data karyawan.',
            ], 500);
        }
    }
    public function import(Request $request)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|extensions:xlsx,xls,csv',
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.extensions' => 'File harus berformat xlsx, xls, atau csv.',
        ]);

        try {
            Excel::import(new KaryawanImport(), $request->file('file'));
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diimpor.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error importing karyawan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengimpor data karyawan.',
            ], 500);
        }
    }
    public function destroy(Karyawan $karyawan)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403);
        }
        try {
            $karyawan->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error deleting karyawan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data karyawan.',
            ], 500);
        }
    }
}
