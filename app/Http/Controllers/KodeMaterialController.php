<?php

namespace App\Http\Controllers;

use App\Models\WiTrnBpm;
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

        $query = WiTrnBpm::with('user')->select([
            'id', 'id_wi_trn_bpm', 'spec', 'ket', 'user_id',
        ]);

        if ($request->filled('id_wi_trn_bpm')) {
            $query->where('id_wi_trn_bpm', 'LIKE', '%' . $request->id_wi_trn_bpm . '%');
        }

        if ($request->filled('spec')) {
            $query->where('spec', 'LIKE', '%' . $request->spec . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('user', function ($row) {
                return $row->user?->name ?? '-';
            })
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
        $kodeMaterial = WiTrnBpm::findOrFail($id);

        return view('master.kode-material.edit', compact('kodeMaterial'));
    }

    public function update(Request $request, $id)
    {
        $kodeMaterial = WiTrnBpm::findOrFail($id);

        $validated = $request->validate([
            'id_wi_trn_bpm' => 'required|string|max:255',
            'spec' => 'nullable|string',
            'ket' => 'nullable|string',
        ]);

        $kodeMaterial->update($validated);

        return redirect()->route('kode-material.index')
            ->with('success', 'Data kode material berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kodeMaterial = WiTrnBpm::findOrFail($id);
        $kodeMaterial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kode material berhasil dihapus.',
        ]);
    }
}
