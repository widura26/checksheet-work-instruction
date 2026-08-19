<?php

namespace App\Http\Controllers;

use App\Exports\ToolDibuangExport;
use App\Models\DaftarToolDibuang;
use App\Models\ToolDibuang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ToolDibuangController extends Controller
{
    public function index()
    {
        return view('pages.tool-dibuang.index');
    }

    public function create()
    {
        if (!auth()->user()->hasAnyRole(['admin', 'staff'])) {
            abort(403);
        }
        return view('pages.tool-dibuang.create');
    }
    public function edit(ToolDibuang $toolDibuang)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }
        $toolDibuang->load('daftarToolDibuang');
        return view('pages.tool-dibuang.edit', [
            'toolDibuang' => $toolDibuang->daftarToolDibuang,
            'noBs' => $toolDibuang->nomor_bs,
            'toolDibuangId' => $toolDibuang->id
        ]);
    }

    public function datatable(Request $request)
    {
        $query = DaftarToolDibuang::with('toolDibuang')->select('daftar_tool_dibuangs.*');

        if ($request->from_date) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $query->whereHas('toolDibuang', function ($q) use ($fromDate) {
                $q->where('tanggal_dibuang', '>=', $fromDate);
            });
        }

        if ($request->to_date) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->whereHas('toolDibuang', function ($q) use ($toDate) {
                $q->where('tanggal_dibuang', '<=', $toDate);
            });
        }
        if ($request->filter_bukti) {
            if ($request->filter_bukti === 'sudah_upload') {
                $query->whereHas('toolDibuang', function ($q) {
                    $q->whereNotNull('bukti_file');
                });
            } elseif ($request->filter_bukti === 'belum_upload') {
                $query->whereHas('toolDibuang', function ($q) {
                    $q->whereNull('bukti_file');
                });
            }
        }
        $firstIds = DaftarToolDibuang::select(DB::raw('MIN(id) as id'))
            ->groupBy('tool_dibuang_id')
            ->pluck('id')
            ->toArray();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_dibuang', function ($row) {
                return \Carbon\Carbon::parse($row->toolDibuang->tanggal_dibuang)->format('d-m-Y H:i');
            })
            ->addColumn('nomor_bs', function ($row) {
                return $row->toolDibuang->nomor_bs;
            })
            ->addColumn('action', function ($row) use ($firstIds) {
                if (! in_array($row->id, $firstIds)) {
                    return '';
                }
                $btn = '<div class="d-flex justify-content-center gap-2">';
                if (! $row->toolDibuang->bukti_file) {
                    $btn .= '
                    <span class="d-none">upload bukti</span>
                    <button type="button" class="btn btn-primary btn-sm text-white" 
                                data-coreui-toggle="modal" 
                                data-coreui-target="#uploadModal" 
                                data-id="' . $row->tool_dibuang_id . '">
                                <i class="fas fa-upload"></i> Upload Bukti
                            </button>';
                } else {
                    $btn .= '
                    <span class="d-none">lihat bukti</span>
                    <a href="' . Storage::url($row->toolDibuang->bukti_file) . '" target="_blank" class="btn btn-info btn-sm text-white">
                                <i class="fas fa-file-alt"></i> Lihat Bukti
                            </a>';
                }
                //untuk ubah file, tetap pakai function yang sama dengan upload file pertama
                if ($row->toolDibuang->bukti_file && auth()->user()->hasRole('admin')) {
                    $btn .= '
                    <span class="d-none">ubah bukti</span>
                    <button type="button" class="btn btn-primary btn-sm text-white" 
                                data-coreui-toggle="modal" 
                                data-coreui-target="#uploadModal" 
                                data-id="' . $row->tool_dibuang_id . '">
                                <i class="fas fa-upload"></i> Ubah Bukti
                            </button>';
                }
                // untuk update hanya admin
                if (auth()->user()->hasRole('admin')) {
                    $btn .= '
                    <span class="d-none">edit</span>
                    <a href="' . route('tool-dibuang.edit', $row->tool_dibuang_id) . '" class="btn btn-warning btn-sm text-white">
                                <i class="fas fa-edit"></i> Edit
                            </a>';
                }

                $btn .= '</div>';
                return $btn;
            })
            ->filterColumn('nomor_bs', function ($query, $keyword) {
                $query->whereHas('toolDibuang', function ($q) use ($keyword) {
                    $q->where('nomor_bs', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('kode_material', function ($query, $keyword) {
                $query->where('kode_material', 'like', "%{$keyword}%");
            })
            ->filterColumn('deskripsi', function ($query, $keyword) {
                $query->where('deskripsi', 'like', "%{$keyword}%");
            })
            ->filterColumn('spesifikasi', function ($query, $keyword) {
                $query->where('spesifikasi', 'like', "%{$keyword}%");
            })
            ->filterColumn('detail_kerusakan', function ($query, $keyword) {
                $query->where('detail_kerusakan', 'like', "%{$keyword}%");
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function export(Request $request)
    {

        $filters = [
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ];

        return Excel::download(new ToolDibuangExport($filters), 'tool-dibuang-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function uploadBukti(Request $request)
    {
        // bisa digunakan untuk edit
        if (!auth()->user()->hasAnyRole(['admin', 'staff'])) {
            abort(403);
        }
        $request->validate([
            'tool_dibuang_id' => 'required|exists:tool_dibuangs,id',
            'bukti_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $toolDibuang = ToolDibuang::findOrFail($request->tool_dibuang_id);
        // Jika sudah ada file sebelumnya, hanya admin yang boleh update
        if ($toolDibuang->bukti_file && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
        if ($request->hasFile('bukti_file')) {
            $path = $request->file('bukti_file')->store('bukti_tool_dibuang', 'public');

            if ($toolDibuang->bukti_file) {
                Storage::disk('public')->delete($toolDibuang->bukti_file);
            }

            $toolDibuang->bukti_file = $path;
            $toolDibuang->save();
        }

        return redirect()->back()->with('success', 'File bukti berhasil diupload.');
    }
}
