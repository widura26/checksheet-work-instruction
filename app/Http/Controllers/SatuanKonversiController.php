<?php

namespace App\Http\Controllers;

use App\Models\SatuanKonversi;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SatuanKonversiController extends Controller
{
    public function index()
    {
        $konversi = SatuanKonversi::all();
        return view('pages.konversi.index', compact('konversi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_material' => 'required|unique:satuan_konversi,kode_material',
            'satuan_sap' => 'required',
            'satuan_distribusi' => 'required',
            'berat_per_satuan_distribusi' => 'required|numeric',
        ]);

        // Hitung nilai konversi (misal: 1 Pcs = 12 Gram, Gram ke KG = 0.001. Jadi 12 * 0.001 = 0.012)
        // Kita simpan saja sesuai logic user atau request.
        // Di sini saya asumsikan nilai_konversi dihitung otomatis jika dibutuhkan, 
        // tapi user menghapus input nilai_konversi di view.
        // Untuk sementara kita set nilai_konversi dari berat_per_satuan_distribusi / 1000 jika SAP nya KG.
        
        $nilai_konversi = $request->berat_per_satuan_distribusi;
        if (strtoupper($request->satuan_sap) == 'KG') {
            $nilai_konversi = $request->berat_per_satuan_distribusi / 1000;
        }

        SatuanKonversi::create([
            'kode_material' => $request->kode_material,
            'satuan_sap' => $request->satuan_sap,
            'satuan_distribusi' => $request->satuan_distribusi,
            'berat_per_satuan_distribusi' => $request->berat_per_satuan_distribusi,
            'nilai_konversi' => $nilai_konversi,
        ]);

        return redirect()->route('konversi-satuan.index')->with('success', 'Berhasil menyimpan data konversi.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_material' => 'required|unique:satuan_konversi,kode_material,' . $id,
            'satuan_sap' => 'required',
            'satuan_distribusi' => 'required',
            'berat_per_satuan_distribusi' => 'required|numeric',
        ]);

        $konversi = SatuanKonversi::findOrFail($id);
        
        $nilai_konversi = $request->berat_per_satuan_distribusi;
        if (strtoupper($request->satuan_sap) == 'KG') {
            $nilai_konversi = $request->berat_per_satuan_distribusi / 1000;
        }

        $konversi->update([
            'kode_material' => $request->kode_material,
            'satuan_sap' => $request->satuan_sap,
            'satuan_distribusi' => $request->satuan_distribusi,
            'berat_per_satuan_distribusi' => $request->berat_per_satuan_distribusi,
            'nilai_konversi' => $nilai_konversi,
        ]);

        return redirect()->route('konversi-satuan.index')->with('success', 'Berhasil memperbarui data konversi.');
    }

    public function destroy($id)
    {
        $konversi = SatuanKonversi::findOrFail($id);
        $konversi->delete();

        return response()->json(['success' => 'Berhasil menghapus data konversi.']);
    }
}
