<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KodeBagianController extends Controller
{
    public function __construct()
    {
        //hanya boleh role admin
        $this->middleware('role:admin');
    }
    public function index()
    {
        $kodeBagian = DB::table('kode_bagian_spm')->first();
        $departemen = DB::table('departemens')->get();
        $bagian = DB::table('bagians')->get();
        $unit = DB::table('units')->get();
        return view('pages.kode-bagian.index', compact('kodeBagian', 'departemen', 'bagian', 'unit'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nama_manager' => 'required|string|max:255',
            'stok_rendah' => 'required|integer|min:0',
            'departemen_id' => 'required|exists:departemens,id',
            'bagian_id' => 'required|exists:bagians,id',
            'unit_id' => 'required|exists:units,id',

        ], [
            'kode.required' => 'Kode wajib diisi.',
            'kode.string' => 'Kode harus berupa teks.',
            'kode.max' => 'Kode maksimal 255 karakter.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'jabatan.string' => 'Jabatan harus berupa teks.',
            'jabatan.max' => 'Jabatan maksimal 255 karakter.',
            'nama_manager.required' => 'Nama Manager wajib diisi.',
            'nama_manager.string' => 'Nama Manager harus berupa teks.',
            'nama_manager.max' => 'Nama Manager maksimal 255 karakter.',
            'stok_rendah.required' => 'Stok Rendah wajib diisi.',
            'stok_rendah.integer' => 'Stok Rendah harus berupa angka.',
            'stok_rendah.min' => 'Stok Rendah minimal 0.',
            'departemen_id.required' => 'Departemen wajib diisi.',
            'departemen_id.exists' => 'Departemen tidak ditemukan.',
            'bagian_id.required' => 'Bagian wajib diisi.',
            'bagian_id.exists' => 'Bagian tidak ditemukan.',
            'unit_id.required' => 'Unit wajib diisi.',
            'unit_id.exists' => 'Unit tidak ditemukan.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $kodeBagian = DB::table('kode_bagian_spm')->first();

            if (!$kodeBagian) {
                // Jika tidak ada data, buat baru
                DB::table('kode_bagian_spm')->insert([
                    'kode' => $request->kode,
                    'jabatan' => $request->jabatan,
                    'nama_manager' => $request->nama_manager,
                    'stok_rendah' => $request->stok_rendah,
                    'departemen_id' => $request->departemen_id,
                    'bagian_id' => $request->bagian_id,
                    'unit_id' => $request->unit_id,
                ]);
            } else {
                // Update data yang ada
                DB::table('kode_bagian_spm')
                    ->where('id', $kodeBagian->id)
                    ->update([
                        'kode' => $request->kode,
                        'jabatan' => $request->jabatan,
                        'nama_manager' => $request->nama_manager,
                        'stok_rendah' => $request->stok_rendah,
                        'departemen_id' => $request->departemen_id,
                        'bagian_id' => $request->bagian_id,
                        'unit_id' => $request->unit_id,
                    ]);
            }

            return redirect()->route('kode-bagian.index')
                ->with('success', 'Berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data')
                ->withInput();
        }
    }
}
