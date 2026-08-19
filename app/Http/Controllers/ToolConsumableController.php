<?php

namespace App\Http\Controllers;

use App\Models\DaftarKodeMaterial;
use App\Models\DetailKerusakanTool;
use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use App\Exports\ToolsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ToolConsumableController extends Controller
{
    public function getMaterialDetail(Request $request)
    {
        $material = DaftarKodeMaterial::where('kode_material', $request->kode_material)->first();

        if ($material) {
            return response()->json([
                'deskripsi' => $material->deskripsi,
                'spesifikasi' => $material->spesifikasi,
                'satuan' => $material->satuan,
            ]);
        }

        return response()->json(['error' => 'Material not found'], 404);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = Project::whereNull('closed_at')->get();
        $posisiCTs = Posisi_CT::orderBy('nama_posisi_ct')->get();
        if ($request->ajax()) {
            $query = ToolProjectStock::whereHas('tool', function ($q) {
                $q->where('jenis_material', 'consumable');
            })->whereHas('project', function ($q) {
                $q->whereNull('closed_at');
            })
                ->with(['tool', 'project', 'posisiCT']);
            if ($request->filled('kode_material')) {
                $query->whereHas('tool', function ($q) use ($request) {
                    $q->where('kode_material', 'like', "{$request->input('kode_material')}");
                });
            }
            if ($request->filled('deskripsi')) {
                $query->whereHas('tool', function ($q) use ($request) {
                    $q->where('deskripsi', 'like', '%' . $request->deskripsi . '%');
                });
            }
            if ($request->filled('spesifikasi')) {
                $query->whereHas('tool', function ($q) use ($request) {
                    $q->where('spesifikasi', 'like', '%' . $request->spesifikasi . '%');
                });
            }
            if ($request->filled('posisi_ct_id')) {
                $query->where('posisi_ct_id', $request->input('posisi_ct_id'));
            }

            if ($request->filled('project_id')) {
                $query->where('project_id', $request->input('project_id'));
            }
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('kode_material', fn($row) => $row->tool->kode_material)
                ->addColumn('deskripsi', fn($row) => $row->tool->deskripsi)
                ->addColumn('spesifikasi', fn($row) => $row->tool->spesifikasi)
                ->addColumn('satuan', fn($row) => $row->tool->satuan)
                ->addColumn('nama_posisi_ct', function ($row) {
                    return optional($row->posisiCT)->nama_posisi_ct ?? '';
                })
                ->addColumn('project', fn($row) => $row->project->nama_project)
                ->addColumn('stok', fn($row) => $row->qty_formatted)
                ->orderColumn('project', function ($query, $order) {
                    $query->join('projects', 'projects.project_id', '=', 'tool_project_stocks.project_id')
                        ->orderBy('projects.nama_project', $order);
                })
                ->orderColumn('stok', function ($query, $order) {
                    $query->orderByRaw('CAST(tool_project_stocks.qty AS UNSIGNED) ' . $order);
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('tool.edit', [$row->tool->public_id, 'project_id' => $row->project->project_id]);
                    $showUrl = route('tool.show', [$row->tool->public_id, $row->project->project_id, $row->tool->posisi_ct_id]);
                    $eyeIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-fullscreen"></use></svg>';
                    $editIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-pencil"></use></svg>';
                    $trashIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-trash"></use></svg>';
                    $buttons = '';
                    $buttons .= '<div class="btn-group btn-group-sm d-flex justify-content-center gap-2">';
                    $buttons .= ' <a href="' . $showUrl . '" class="btn btn-info btn-sm text-white">' . $eyeIcon . '</a>';
                    if (Auth::user()->hasRole('admin')) {
                        $buttons .= ' <a href="' . $editUrl . '" class="btn btn-warning btn-sm text-white">' . $editIcon . '</a>';
                    }
                    return $buttons;
                })->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.consumable.index', compact('posisiCTs', 'projects'));
    }

    /**
     * Export consumable data to Excel
     */
    public function export(Request $request)
    {
        if (! Auth::user()->hasRole(['admin'])) {
            abort(403);
        }

        $filters = $request->only(['kode_material', 'deskripsi', 'spesifikasi', 'posisi_ct_id', 'project_id']);
        $time = now()->format('Y-m-d_H-i-s');

        return Excel::download(new ToolsExport($filters, 'consumable'), "{$time}_daftar_consumable.xlsx");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posisiCt = Posisi_CT::all();

        return view('pages.consumable.create', compact('posisiCt'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_material' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'spesifikasi' => 'nullable|string|max:255',
            'qty' => 'required|integer',
            'satuan' => 'required|string|max:255',
            'posisi_ct_id' => 'required|exists:posisi__c_t_s,id',
            'vendor' => 'nullable|in:vendor,INKA',
            'keterangan_vendor' => 'nullable|string|max:1000',
        ]);
        try {
            Tools::create(array_merge($request->all(), ['jenis_material' => 'consumable']));

            return redirect()->route('consumable.index')->with('success', 'Consumable Tool berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui tool: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    // public function show(Tools $consumable)
    // {
    //     $tool = $consumable;
    //     $distribusiHistory = $tool->load([
    //         'daftarDistribusiTools.distribusiTool.unit',
    //         'daftarDistribusiTools.distribusiTool.bagian',
    //         'updateStockTools.user',
    //         'penguranganTools.user'
    //     ]);
    //     // ambil data kerusakan dari detail daftar distribusi tool
    //     $daftarDistribusiIds = $tool->daftarDistribusiTools()->pluck('id');
    //     $detailDaftarDistribusiIds = \App\Models\DetailDaftarDistribusiTool::whereIn('daftar_distribusi_tool_id', $daftarDistribusiIds)->pluck('id');
    //     $damageHistory = DetailKerusakanTool::whereIn('detail_daftar_distribusi_tools_id', $detailDaftarDistribusiIds)->latest()->get();
    //     return view('pages.consumable.show', [
    //         'tool' => $tool,
    //         'updateHistory' => $tool->updateStockTools,
    //         'penguranganHistory' => $tool->penguranganTools,
    //         'distribusiHistory' => $distribusiHistory,
    //         'damageHistory' => $damageHistory,
    //     ]);
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tools $consumable) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tools $consumable) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tools $consumable) {}
}
