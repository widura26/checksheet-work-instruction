<?php

namespace App\Http\Controllers;

use App\Models\PenggantianTool;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PenggantianToolController extends Controller
{
    public function index()
    {
        return view('pages.penggantian-tool.index');
    }

    public function data(Request $request)
    {
        $data = PenggantianTool::with('tool')->select('penggantian_tools.*');
        $printIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-print"></use></svg>';

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tool', function (PenggantianTool $penggantianTool) {
                return $penggantianTool->tool->deskripsi;
            })
            ->addColumn('kode_material', function (PenggantianTool $penggantianTool) {
                return $penggantianTool->tool->kode_material;
            })
            ->addColumn('unit', function (PenggantianTool $penggantianTool) {
                return $penggantianTool->unit->nama_unit;
            })
            ->editColumn('status', function (PenggantianTool $penggantianTool) {
                $status = strtoupper($penggantianTool->status);
                return '<span class="badge bg-' . ($penggantianTool->status == 'open' ? 'success' : 'secondary') . '">' . $status . '</span>';
            })
            ->editColumn('file', function (PenggantianTool $penggantianTool) use ($printIcon) {
                return '<a href="' . asset('storage/' . $penggantianTool->file) . '" target="_blank" class="btn btn-success text-white">' . $printIcon . '</a>';
            })
            ->addColumn('action', function ($row) use ($printIcon) {
                if ($row->status === 'open') {
                    return '
                        <button type="button"
                            class="btn btn-warning btn-sm"
                            data-coreui-toggle="modal"
                            data-coreui-target="#updateStatusModal"
                            data-id="' . $row->id . '"
                            data-status="' . $row->status . '">
                            Ubah Status
                        </button>
                    ';
                }
                return '<a href="' . asset('storage/' . $row->file_bukti_penyerahan) . '" target="_blank" class="btn btn-success text-white">' . $printIcon . '</a>';
            })
            ->rawColumns(['action', 'status', 'file'])
            ->make(true);
    }
    public function updateStatus(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|exists:penggantian_tools,id',
                'file_bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            $penggantianTool = PenggantianTool::findOrFail($request->id);

            if ($request->hasFile('file_bukti')) {
                $filePath = $request->file('file_bukti')->store('bukti-penyerahan-penggantian', 'public');
                $penggantianTool->file_bukti_penyerahan = $filePath;
            }
            $penggantianTool->status = 'close';
            $penggantianTool->save();

            return redirect()->route('penggantian-tool.index')->with('success', 'Status penggantian tool berhasil diperbarui.');
        } catch (\Throwable $e) {
            return redirect()->route('penggantian-tool.index')->with('error', 'Gagal memperbarui status penggantian tool.');
        }
    }
}
