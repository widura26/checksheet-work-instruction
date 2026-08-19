<?php

namespace App\Http\Controllers;

use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use App\Exports\ToolsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ToolApdController extends Controller
{
    public function index(Request $request)
    {
        $posisiCTs = Posisi_CT::orderBy('nama_posisi_ct')->get();
        $projects = Project::whereNull('closed_at')->get();
        if ($request->ajax()) {
            $query = ToolProjectStock::whereHas('tool', function ($q) {
                $q->where('jenis_material', 'apd');
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
                    $query->orderBy('tool_project_stocks.qty', $order);
                })
                ->addColumn('action', function ($row) {
                    $showUrl = route('tool.show', [$row->tool->public_id, $row->project->project_id, $row->tool->posisi_ct_id]);
                    $editUrl = route('tool.edit', [$row->tool->public_id, 'project_id' => $row->project->project_id]);
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
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.apd.index', compact('posisiCTs', 'projects'));
    }

    /**
     * Export APD data to Excel
     */
    public function export(Request $request)
    {
        if (! Auth::user()->hasRole(['admin'])) {
            abort(403);
        }

        $filters = $request->only(['kode_material', 'deskripsi', 'spesifikasi', 'posisi_ct_id', 'project_id']);
        $time = now()->format('Y-m-d_H-i-s');

        return Excel::download(new ToolsExport($filters, 'apd'), "{$time}_daftar_apd.xlsx");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tools $apd) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tools $apd) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tools $apd) {}
}
