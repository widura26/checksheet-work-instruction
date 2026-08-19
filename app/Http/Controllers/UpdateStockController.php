<?php

namespace App\Http\Controllers;

use App\Exports\UpdateStockToolExport;
use App\Models\Project;
use App\Models\Spm;
use App\Models\Tools;
use App\Models\UpdateStockTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UpdateStockController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('nama_project', 'asc')->get();

        return view('pages.update_stock_tool.index', compact('projects'));
    }

    public function getData(Request $request)
    {
        $data = UpdateStockTool::with(['user:id,name', 'tool.posisiCT', 'project'])
            ->orderBy('update_stock_tools.created_at', 'desc');

        if ($request->has('kode_material') && ! empty($request->kode_material)) {
            $data->whereHas('tool', function ($q) use ($request) {
                $q->where('kode_material', 'like', '%' . $request->kode_material . '%');
            });
        }

        if ($request->has('project_id') && ! empty($request->project_id)) {
            $data->where('project_id', $request->project_id);
        }

        if ($request->has('start_date') && ! empty($request->start_date)) {
            $data->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && ! empty($request->end_date)) {
            $data->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->has('vendor') && ! empty($request->vendor)) {
            $data->where('vendor', $request->vendor);
        }

        if ($request->has('keterangan_vendor') && ! empty($request->keterangan_vendor)) {
            $data->where('keterangan_vendor', 'LIKE', '%' . $request->keterangan_vendor . '%');
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('tipe', function ($row) {
                if ($row->type === 'input_material') {
                    return '<span class="badge bg-primary">Input Material</span>';
                }
                if ($row->type === 'tambah_stok') {
                    return '<span class="badge bg-success">Tambah Stok</span>';
                }
            })
            ->addColumn('project', function ($row) {
                return $row->project->nama_project ?? '-';
            })
            ->addColumn('dokumen_pengambilan', function ($row) {
                if (Auth::user()->hasRole(['admin', 'staff'])) {

                    $dokumen_pengambilan = ucfirst($row->dokumen_pengambilan ?? '-');
                    $dokumen_file = $row->dokumen_pengambilan_path;

                    if ($dokumen_file) {
                        $fileUrl = asset('storage/' . $dokumen_file);
                        $button = '<a href="' . $fileUrl . '" target="_blank" class="btn btn-sm btn-warning" title="Lihat Dokumen">
                    <svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-print"></use></svg>
                    File
                </a>';
                    } else {
                        $spm = Spm::where('no_spm', $row->nomor_dokumen)->first();
                        if ($spm) {
                            $button = '<a href="' . route('spm.download', $spm->id) . '" target="_blank" class="btn btn-sm btn-warning" title="Lihat SPM">
                    <svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-print"></use></svg>
                    SPM
                </a>';
                        } else {
                            $button = '<span class="text-secondary">Tidak ada file</span>';
                        }
                    }

                    return '<div class="d-flex flex-column gap-1">'
                        . '<span>' . $dokumen_pengambilan . '</span>'
                        . $button
                        . '</div>';
                }
            })
            ->addColumn('ct', function ($row) {
                return $row->tool->posisiCT->nama_posisi_ct ?? '-';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y H:i:s');
            })
            ->rawColumns(['tipe', 'dokumen_pengambilan'])
            ->make(true);
    }

    public function export(Request $request)
    {
        $filters = $request->only([
            'kode_material',
            'project_id',
            'start_date',
            'end_date',
            'vendor',
            'keterangan_vendor',
        ]);

        return Excel::download(new UpdateStockToolExport($filters), 'update-stok-material-' . now()->format('d-m-Y') . '.xlsx');
    }

    public function create()
    {
        if (! Auth::user()->hasRole(['admin', 'staff'])) {
            abort(403);
        }

        return view('pages.update_stock_tool.create');
    }

    public function getToolDetails($kode_material)
    {
        try {
            // Validate material code parameter
            if (empty($kode_material) || ! is_string($kode_material)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode material tidak valid',
                    'code' => 'INVALID_MATERIAL_CODE',
                ], 400);
            }

            $tool = Tools::where('kode_material', $kode_material)->first();

            if ($tool) {
                return response()->json([
                    'success' => true,
                    'mode' => 'update',
                    'data' => [
                        'kode_material' => $tool->kode_material,
                        'deskripsi' => $tool->deskripsi,
                        'spesifikasi' => $tool->spesifikasi,
                        'qty' => $tool->qty,
                        'satuan' => $tool->satuan,
                        'jenis_material' => $tool->jenis_material,
                        'vendor' => $tool->vendor,
                        'keterangan_vendor' => $tool->keterangan_vendor,
                        'posisi_ct_id' => $tool->posisi_ct_id,
                        'tanggal_ditambahkan' => $tool->tanggal_ditambahkan,
                    ],
                    'message' => 'Tool ditemukan - mode update',
                ]);
            }

            // Tool doesn't exist, check DaftarKodeMaterial for create mode
            $material = \App\Models\DaftarKodeMaterial::where('kode_material', $kode_material)->first();

            if ($material) {
                // Material exists in master data - return data for create mode
                return response()->json([
                    'success' => true,
                    'mode' => 'create',
                    'data' => [
                        'kode_material' => $material->kode_material,
                        'deskripsi' => $material->deskripsi,
                        'spesifikasi' => $material->spesifikasi,
                        'satuan' => $material->satuan,
                    ],
                    'message' => 'Material ditemukan di master data - mode create',
                ]);
            }

            // Material not found in either table
            return response()->json([
                'success' => false,
                'message' => 'Kode material tidak ditemukan di database',
                'code' => 'MATERIAL_NOT_FOUND',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in getToolDetails: ' . $e->getMessage(), [
                'kode_material' => $kode_material,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'code' => 'SYSTEM_ERROR',
            ], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'tools' => 'required|array|min:1',
    //         'tools.*.kode_material' => 'required|string|exists:tools,kode_material',
    //         'tools.*.qty_ditambah' => 'required|integer|min:1',
    //     ]);
    //     DB::beginTransaction();
    //     try {
    //         foreach ($validated['tools'] as $toolData) {
    //             $tool = Tools::where('kode_material', $toolData['kode_material'])->lockForUpdate()->first();
    //             $qtyBefore = $tool->qty;
    //             if (!$tool) {
    //                 throw new \Exception("Tool dengan kode material {$toolData['kode_material']} tidak ditemukan.");
    //             }

    //             // Penambahan Stok
    //             $tool->increment('qty', $toolData['qty_ditambah']);

    //             UpdateStockTool::create([
    //                 'user_id' => Auth::id(),
    //                 'kode_material' => $tool->kode_material,
    //                 'qty_sebelum' => $qtyBefore,
    //                 'qty_update' => $toolData['qty_ditambah'],
    //                 'qty_sesudah' => $tool->qty + (int)$toolData['qty_ditambah'],
    //                 'tanggal_update' => now(),
    //             ]);
    //         }
    //         DB::commit();
    //         return redirect()->route('update-stok-tool.index')->with('success', 'Stok tool berhasil ditambahkan.');
    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan stok: ' . $e->getMessage())->withInput();
    //     }
    // }
}
