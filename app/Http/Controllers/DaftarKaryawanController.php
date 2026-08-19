<?php

namespace App\Http\Controllers;

use App\Http\Requests\DaftarKaryawanRequest;
use App\Imports\KaryawanImport;
use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Unit;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;

class DaftarKaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $karyawans = Karyawan::with(['departemen', 'bagian', 'unit']);
        if ($search) {
            $karyawans->where(function ($query) use ($search) {
                $query->where('nip', 'like', "%{$search}%")
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
        if ($status == 'kosong') {
            $karyawans->whereNull('departemen_id')
                ->orWhereNull('bagian_id')
                ->orWhereNull('unit_id');
        }
        $karyawans = $karyawans->paginate(10)->withQueryString();
        $departemens = Departemen::all();
        $bagians = Bagian::all();
        $units = Unit::all();
        return view('pages.karyawan.index', compact('karyawans', 'departemens', 'bagians', 'units'));
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
            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            Log::error('Error creating karyawan: ' . $e->getMessage());
            return redirect()->route('karyawan.index')->with('error', 'Gagal menambahkan data karyawan');
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
            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Error updating karyawan: ' . $e->getMessage());
            return redirect()->route('karyawan.index')->with('error', 'Gagal memperbarui data karyawan');
        }
    }
    public function import(Request $request)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403);
        }
        try {
            Excel::import(new KaryawanImport(), $request->file('file'));
            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil diimpor.');
        } catch (\Throwable $e) {
            Log::error('Error importing karyawan: ' . $e->getMessage());
            return redirect()->route('karyawan.index')->with('error', 'Gagal mengimpor data karyawan');
        }
    }
    public function destroy(Karyawan $karyawan)
    {
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403);
        }
        try {
            $karyawan->delete();
            return redirect()->route('karyawan.index')->with('success', 'Data karyawan berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Error deleting karyawan: ' . $e->getMessage());
            return redirect()->route('karyawan.index')->with('error', 'Gagal menghapus data karyawan');
        }
    }
}
