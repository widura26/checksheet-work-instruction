<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Posisi_CT;
use Illuminate\Http\Request;

class CentralToolController extends Controller
{
    public function index()
    {
        $data = Posisi_CT::with('departemen')->latest()->get();
        return view('pages.posisi-ct.index', compact('data'));
    }

    public function create()
    {
        $departemens = Departemen::all();

        return view('pages.posisi-ct.create', compact('departemens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_posisi_ct' => 'required|string|max:255',
            'departemen_id' => 'nullable',
        ], [
            'nama_posisi_ct.required' => 'Nama posisi harus diisi.',
        ]);
        $departemenId = $request->departemen_id === 'uu' ? null : $request->departemen_id;
        if ($departemenId && Posisi_CT::where('departemen_id', $departemenId)->exists()) {
            return redirect()->back()->with('error', 'Posisi CT sudah ada untuk departemen ini.');
        }
        if ($departemenId === null && Posisi_CT::whereNull('departemen_id')->exists()) {
            return redirect()->back()->with('error', 'Posisi CT untuk Stok Bebas sudah ada.');
        }
        Posisi_CT::create([
            'nama_posisi_ct' => $request->nama_posisi_ct,
            'departemen_id' => $departemenId,
        ]);

        return redirect()->route('posisi-ct.index')->with('success', 'Data posisi berhasil ditambahkan.');
    }

    public function edit(Posisi_CT $posisiCt)
    {
        $departemens = Departemen::all();

        return view('pages.posisi-ct.edit', compact('posisiCt', 'departemens'));
    }

    public function update(Request $request, Posisi_CT $posisiCt)
    {
        $request->validate([
            'nama_posisi_ct' => 'required|string|max:255',
            'departemen_id' => 'nullable',
        ], [
            'nama_posisi_ct.required' => 'Nama posisi harus diisi.',
        ]);

        $departemenId = $request->departemen_id === 'uu' ? null : $request->departemen_id;
        if ($departemenId && Posisi_CT::where('departemen_id', $departemenId)->where('id', '!=', $posisiCt->id)->exists()) {
            return redirect()->back()->with('error', 'Posisi CT sudah ada untuk departemen ini.');
        }
        if ($departemenId === null && Posisi_CT::whereNull('departemen_id')->exists()) {
            return redirect()->back()->with('error', 'Posisi CT untuk Stok Bebas sudah ada.');
        }
        $posisiCt->update([
            'nama_posisi_ct' => $request->nama_posisi_ct,
            'departemen_id' => $departemenId,
        ]);

        return redirect()->route('posisi-ct.index')->with('success', 'Data posisi berhasil diperbarui.');
    }

    public function destroy(Posisi_CT $posisiCt)
    {
        $posisiCt->delete();

        return redirect()->route('posisi-ct.index')->with('success', 'Data posisi berhasil dihapus.');
    }
}
