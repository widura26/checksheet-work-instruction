<?php

namespace App\Http\Controllers;

use App\Models\DetailKerusakanTool;
use App\Models\ToolProjectStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ToolRusakController extends Controller
{
    public function index()
    {
        $detailKerusakanTools = DetailKerusakanTool::with('detailDaftarDistribusiTool.daftarDistribusiTool.tool')->get();

        return view('pages.tool-rusak.index', compact('detailKerusakanTools'));
    }

    public function datatable(Request $request)
    {
        $query = DetailKerusakanTool::with(['detailDaftarDistribusiTool.daftarDistribusiTool.tool', 'detailDaftarDistribusiTool.daftarDistribusiTool.distribusiTool.project'])->select('detail_kerusakan_tools.*')
            ->where('status_perbaikan', '<>', 'tidak_bisa_diperbaiki')
            ->orderBy('created_at', 'desc');
        // Filter by status perbaikan
        if ($request->has('status_filter') && $request->status_filter != '') {
            $statusMap = [
                'Menunggu SPR' => 'menunggu_spr',
                'Sedang Diperbaiki' => 'sedang_diperbaiki',
                'Sudah Diperbaiki' => 'selesai',
                'Tidak Dapat Diperbaiki' => 'tidak_bisa_diperbaiki',
            ];

            if (isset($statusMap[$request->status_filter])) {
                $query->where('status_perbaikan', $statusMap[$request->status_filter]);
            }
        }

        // Filter by kode material
        if ($request->has('kode_material_filter') && $request->kode_material_filter != '') {
            $query->whereHas('detailDaftarDistribusiTool.daftarDistribusiTool.tool', function ($q) use ($request) {
                $q->where('kode_material', 'like', '%' . $request->kode_material_filter . '%');
            });
        }

        // Filter by tanggal SPR
        if ($request->has('tanggal_dari_filter') && $request->tanggal_dari_filter != '') {
            $query->whereDate('tanggal_spr', '>=', $request->tanggal_dari_filter);
        }

        if ($request->has('tanggal_sampai_filter') && $request->tanggal_sampai_filter != '') {
            $query->whereDate('tanggal_spr', '<=', $request->tanggal_sampai_filter);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tool.kode_material', function ($row) {
                return $row->detailDaftarDistribusiTool->daftarDistribusiTool->tool ? $row->detailDaftarDistribusiTool->daftarDistribusiTool->tool->kode_material : '-';
            })
            ->addColumn('tool.deskripsi', function ($row) {
                return $row->detailDaftarDistribusiTool->daftarDistribusiTool->tool ? $row->detailDaftarDistribusiTool->daftarDistribusiTool->tool->deskripsi : '-';
            })
            ->addColumn('lokasi', function ($row) {
                return $row->detailDaftarDistribusiTool->daftarDistribusiTool->tool->posisiCT->nama_posisi_ct ?? '-';
            })
            ->addColumn('project', function ($row) {
                return $row->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->project->nama_project ?? '-';
            })
            ->addColumn('status_perbaikan', function ($row) {
                switch ($row->status_perbaikan) {
                    case 'belum_diperbaiki':
                        return '<span class="badge bg-secondary text-white">Belum Diperbaiki</span>';

                    case 'sedang_diperbaiki':
                        return '<span class="badge bg-warning text-white">Sedang Diperbaiki</span>';

                    case 'selesai':
                        return '<span class="badge bg-success text-white">Sudah Diperbaiki</span>';

                    case 'tidak_bisa_diperbaiki':
                        return '<span class="badge bg-danger text-white">Tidak Dapat Diperbaiki</span>';

                    default:
                        return '<span class="badge bg-light text-dark">-</span>';
                }
            })
            ->addColumn('tanggal_spr', function ($row) {
                return $row->tanggal_spr ? $row->tanggal_spr->format('d-m-Y') : '-';
            })
            ->addColumn('file_spr', function ($row) {
                if ($row->file_spr) {
                    return '<a href="' . Storage::url($row->file_spr) . '" target="_blank">Lihat SPR</a>';
                }

                return '-';
            })
            ->addColumn('action', function ($row) {
                $btns = '';
                if (Auth::user()->hasRole(['admin', 'staff'])) {
                    if ($row->status_pengajuan_spr == 'belum') {
                        $btns .= '<button type="button" class="btn btn-primary btn-sm text-white" data-coreui-toggle="modal" data-coreui-target="#uploadModal' . $row->id . '">
                                    Upload SPR
                                </button>';
                    }
                    if ($row->status_perbaikan == 'sedang_diperbaiki') {
                        $btns .= '<span class="badge bg-success text-white mb-2">No SPR : ' . $row->no_spr . '</span>';

                        $btns .= '<form action="' . route('tool-rusak.update-status-perbaikan', $row->id) . '" method="POST" class="mb-3">
                                    ' . csrf_field() . '
                                    ' . method_field('PATCH') . '
                                    <input type="hidden" name="status_perbaikan" value="bisa_diperbaiki">
                                    <button type="submit" class="btn btn-success btn-sm text-white">Bisa Diperbaiki</button>
                                </form>';
                        $btns .= '<form action="' . route('tool-rusak.update-status-perbaikan', $row->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    ' . method_field('PATCH') . '
                                    <input type="hidden" name="status_perbaikan" value="tidak_bisa_diperbaiki">
                                    <button type="submit" class="btn btn-danger btn-sm text-white">Tidak Bisa Diperbaiki</button>
                                </form>';
                    } elseif ($row->status_perbaikan == 'selesai') {
                        $btns .= '<span class="badge bg-success text-white mb-2">' . $row->no_spr . '</span>';
                    }
                }

                return $btns;
            })
            ->rawColumns(['file_spr', 'action', 'status_perbaikan'])
            ->make(true);
    }

    public function uploadSpr(Request $request, $id)
    {
        $request->validate([
            'file_spr' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'no_spr' => 'required|string|max:100',
        ]);

        $detailKerusakanTool = DetailKerusakanTool::findOrFail($id);

        if ($request->hasFile('file_spr')) {
            $path = $request->file('file_spr')->store('public/spr_files');
            if ($detailKerusakanTool->file_spr) {
                Storage::delete($detailKerusakanTool->file_spr);
            }
            $detailKerusakanTool->file_spr = $path;
        }

        $detailKerusakanTool->status_pengajuan_spr = $request->status_pengajuan_spr;
        // update status spr dan update status perbaikan
        $detailKerusakanTool->status_pengajuan_spr = 'sudah';
        $detailKerusakanTool->status_perbaikan = 'sedang_diperbaiki';
        $detailKerusakanTool->tanggal_spr = today();
        $detailKerusakanTool->no_spr = $request->no_spr;
        $detailKerusakanTool->save();

        return redirect()->route('tool-rusak.index')->with('success', 'SPR berhasil diupload dan status diperbarui.');
    }

    public function updateStatusPerbaikan(Request $request, $id)
    {
        $request->validate([
            'status_perbaikan' => 'required|in:bisa_diperbaiki,tidak_bisa_diperbaiki',
        ]);

        try {
            $detailKerusakanTool = DetailKerusakanTool::with(['detailDaftarDistribusiTool.daftarDistribusiTool.tool', 'detailDaftarDistribusiTool.daftarDistribusiTool.distribusiTool.project'])->findOrFail($id);
            if ($request->status_perbaikan == 'tidak_bisa_diperbaiki') {
                $toolData = $detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->tool;
                $project = $detailKerusakanTool->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->project;
                $stok = ToolProjectStock::where('tool_id', $toolData->id)
                    ->where('project_id', $project->project_id)
                    ->where('posisi_ct_id', $toolData->posisi_ct_id)
                    ->firstOrFail();
                return redirect()->route('pengurangan-tool.create', ['publicId' => $detailKerusakanTool->public_id, 'projectId' => $project->project_id])
                    ->with('tool_data', [
                        'kode_material' => $toolData->kode_material,
                        'posisi_ct_id' => $toolData->posisi_ct_id,
                        'tool_id' => $toolData->id,
                        'deskripsi' => $toolData->deskripsi,
                        'stok' => $stok->qty,
                        'satuan' => $toolData->satuan,
                    ]);
            }
            if ($request->status_perbaikan == 'bisa_diperbaiki') {
                $detailKerusakanTool->status_perbaikan = 'selesai';
                $detailKerusakanTool->save();
            }

            return redirect()->route('tool-rusak.index')->with('success', 'Status perbaikan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('tool-rusak.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
