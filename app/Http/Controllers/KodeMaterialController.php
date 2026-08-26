<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KodeMaterialController extends Controller
{
    public function index()
    {
        return view('master.kode-material.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Material::query();

        if ($request->filled('kode_material')) {
            $query->where('kode_material', 'LIKE', '%' . $request->kode_material . '%');
        }

        if ($request->filled('deskripsi')) {
            $query->where('deskripsi', 'LIKE', '%' . $request->deskripsi . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $edit = '<a href="' . route('kode-material.edit', $row->id) . '" '
                    . 'class="btn btn-warning btn-sm text-white" title="Edit">'
                    . '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg#cil-pencil') . '"></use></svg>'
                    . '</a>';

                $delete = '<button class="btn btn-danger btn-sm deleteButton" data-id="' . $row->id . '" '
                    . 'title="Hapus">'
                    . '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg#cil-trash') . '"></use></svg>'
                    . '</button>';

                return '<div class="btn-group" role="group">' . $edit . $delete . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function edit($id)
    {
        $kodeMaterial = Material::findOrFail($id);

        return view('master.kode-material.edit', compact('kodeMaterial'));
    }

    public function update(Request $request, $id)
    {
        $kodeMaterial = Material::findOrFail($id);

        $validated = $request->validate([
            'kode_material' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'spesifikasi' => 'nullable|string',
            'satuan' => 'nullable|string',
        ]);

        $kodeMaterial->update($validated);

        return redirect()->route('kode-material.index')
            ->with('success', 'Data kode material berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kodeMaterial = Material::findOrFail($id);
        $kodeMaterial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kode material berhasil dihapus.',
        ]);
    }
}
