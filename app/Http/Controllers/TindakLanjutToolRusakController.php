<?php

namespace App\Http\Controllers;

use App\Models\DetailKerusakanTool;
use App\Models\Posisi_CT;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TindakLanjutToolRusakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        $lokasi = Posisi_CT::all();

        return view('pages.tindak-lanjut-tool-rusak.index', compact('projects', 'lokasi'));
    }

    /**
     * Get data for DataTables.
     */
    public function data(Request $request)
    {
        $query = DetailKerusakanTool::with([
            'detailDaftarDistribusiTool.daftarDistribusiTool.tool',
            'prDetail.pr',
            'detailDaftarDistribusiTool.daftarDistribusiTool.distribusiTool.project',
        ]);

        if ($request->filled('kode_material')) {
            $query->whereHas('detailDaftarDistribusiTool.daftarDistribusiTool.tool', function ($q) use ($request) {
                $q->where('kode_material', 'like', '%'.$request->kode_material.'%');
            });
        }

        if ($request->filled('project_id')) {
            $query->whereHas('detailDaftarDistribusiTool.daftarDistribusiTool.distribusiTool', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        if ($request->filled('lokasi_id')) {
            $query->whereHas('detailDaftarDistribusiTool.daftarDistribusiTool.tool.PosisiCT', function ($q) use ($request) {
                $q->where('id', $request->lokasi_id);
            });
        }

        if ($request->filled('status_perbaikan')) {
            $query->where('status_perbaikan', $request->status_perbaikan);
        } else {
            $query->where('status_perbaikan', 'tidak_bisa_diperbaiki');
        }

        if ($request->filled('status')) {
            if ($request->status == 'sudah_di_pr') {
                $query->whereHas('prDetail');
            } elseif ($request->status == 'belum_di_pr') {
                $query->whereDoesntHave('prDetail');
            }
        }

        if ($request->filled('keterangan')) {
            $query->where('keterangan', $request->keterangan);
        }
        if ($request->filled('no_spr')) {
            $query->where('no_spr', $request->no_spr);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('kode_material', function ($row) {
                return $row->detailDaftarDistribusiTool->daftarDistribusiTool->tool->kode_material ?? '-';
            })
            ->addColumn('no_spr', function ($row) {
                return $row->no_spr ?? '-';
            })
            ->addColumn('deskripsi_tool', function ($row) {
                return $row->detailDaftarDistribusiTool->daftarDistribusiTool->tool->deskripsi ?? '-';
            })
            ->addColumn('lokasi', function ($row) {
                return $row->detailDaftarDistribusiTool->daftarDistribusiTool->tool->PosisiCT->nama_posisi_ct ?? '-';
            })
            ->addColumn('project', function ($row) {
                return $row->detailDaftarDistribusiTool->daftarDistribusiTool->distribusiTool->project->nama_project ?? '-';
            })
            ->editColumn('status_perbaikan', function ($row) {
                $status = $row->status_perbaikan;
                $badgeClass = 'bg-secondary';
                switch ($status) {
                    case 'tidak_bisa_diperbaiki':
                        $badgeClass = 'bg-danger';
                        break;
                }

                return '<span class="badge '.$badgeClass.'">'.str_replace('_', ' ', ucfirst($status)).'</span>';
            })
            ->addColumn('no_pr', function ($row) {
                if ($row->prDetail && $row->prDetail->pr) {
                    return $row->prDetail->pr->no_pr;
                }

                return '-';
            })
            ->addColumn('status', function ($row) {
                if ($row->prDetail && $row->prDetail->pr) {
                    return '<span class="badge bg-success">
                            No PR '.$row->prDetail->pr->no_pr.'
                        </span>';
                }

                return '<span class="badge bg-danger">Belum di PR</span>';
            })
            ->addColumn('keterangan', function ($row) {
                if ($row->keterangan == 'sudah_diganti') {
                    return '<span class="badge bg-success">
                         Tool Sudah Diganti
                        </span>';
                }

                return '<span class="badge bg-danger">Belum di Ganti</span>';
            })
            ->addColumn('file_spr', function ($row) {
                if ($row->file_spr) {
                    return '<a href="'.Storage::url($row->file_spr).'" target="_blank">Lihat SPR</a>';
                }

                return '-';
            })
            ->rawColumns(['status_pengajuan_spr', 'status_perbaikan', 'status', 'keterangan', 'file_spr'])
            ->make(true);
    }
}
