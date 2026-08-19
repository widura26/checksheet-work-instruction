<?php

namespace App\Http\Controllers;

use App\Models\Spm;
use App\Models\UpdateStockTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ListDokumenPengambilanController extends Controller
{
    public function index()
    {
        return view('pages.dokumen-pengambilan.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = UpdateStockTool::with('project')
                ->whereNotNull('dokumen_pengambilan')
                ->where('dokumen_pengambilan', '!=', 'spm')
                ->whereIn('id', function ($sub) {
                    $sub->selectRaw('MIN(id)')
                        ->from('update_stock_tools')
                        ->groupBy('nomor_dokumen');
                });
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('tanggal_update', function ($row) {
                    return $row->tanggal_update ? \Carbon\Carbon::parse($row->tanggal_update)->format('d-m-Y') : '-';
                })
                ->addColumn('kode_material', function ($row) {
                    return $row->tool->kode_material ?? '-';
                })
                ->addColumn('deskripsi', function ($row) {
                    return $row->tool->deskripsi ?? '-';
                })
                ->addColumn('project', function ($row) {
                    return $row->project->nama_project ?? '-';
                })
                ->editColumn('dokumen_pengambilan', function ($row) {
                    $dokumen_pengambilan = ucfirst($row->dokumen_pengambilan ?? '-');
                    if (strtolower($row->dokumen_pengambilan) === 'memo') {
                        return '<span class="badge bg-success">' . $dokumen_pengambilan . '</span>';
                    } elseif (strtolower($row->dokumen_pengambilan) === 'bpm') {
                        return '<span class="badge bg-info">' . $dokumen_pengambilan . '</span>';
                    } else {
                        return '<span class="badge bg-secondary">' . $dokumen_pengambilan . '</span>';
                    }
                })
                ->editColumn('nomor_dokumen', function ($row) {
                    return $row->nomor_dokumen ?? '-';
                })
                ->addColumn('file_dokumen', function ($row) {
                    $dokumen_file = $row->dokumen_pengambilan_path;

                    if ($dokumen_file && Storage::disk('public')->exists($dokumen_file)) {
                        $fileUrl = asset('storage/' . $dokumen_file);
                        $downloadIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-cloud-download"></use></svg>';
                        return '<a href="' . $fileUrl . '" target="_blank" class="btn btn-sm btn-success text-white" title="Download File">
                            ' . $downloadIcon . '
                        </a>';
                    } else {
                        // Jika dokumen_pengambilan adalah SPM, cek dari tabel spm
                        if ($row->dokumen_pengambilan === 'spm') {
                            $spm = Spm::where('no_spm', $row->nomor_dokumen)->first();
                            if ($spm && $spm->file) {
                                $printUrl = route('spm.print', $spm->id);
                                $printIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-print"></use></svg>';
                                return '<a href="' . $printUrl . '" target="_blank" class="btn btn-sm btn-warning text-white" title="Lihat SPM">
                                    ' . $printIcon . '
                                </a>';
                            }
                        }
                        return '<span class="text-secondary">Tidak ada file</span>';
                    }
                })
                ->addColumn('user', function ($row) {
                    return $row->user->name ?? '-';
                })
                ->rawColumns(['dokumen_pengambilan', 'file_dokumen'])
                ->make(true);
        }
    }
}
