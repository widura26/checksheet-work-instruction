<?php

namespace App\Http\Controllers;

use App\Models\DetailKerusakanTool;
use App\Models\PenguranganTool;
use App\Models\Tools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PenguranganToolController extends Controller
{
    /**
     * Display a listing of the resource (history of reductions).
     */
    public function index()
    {
        return view('pages.pengurangan_tool.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = PenguranganTool::with(['tool', 'project', 'posisiCt',  'user' => function ($q) {
                $q->select('id', 'name');
            }])->select('pengurangan_tools.*')
                ->orderBy('tanggal_pengurangan', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('tool.kode_material', function ($row) {
                    return $row->tool->kode_material;
                })
                ->editColumn('tool.deskripsi', function ($row) {
                    return $row->tool->deskripsi;
                })
                ->editColumn('tanggal_pengurangan', function ($row) {
                    return $row->tanggal_pengurangan->format('d M Y H:i');
                })
                ->addColumn('lokasi', function ($row) {
                    return $row->posisiCt->nama_posisi_ct ?? '-';
                })
                ->addColumn('project', function ($row) {
                    return $row->project->nama_project ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $detailUrl = route('pengurangan-tool.show', $row->public_id);

                    $buttons = '<div class="d-flex justify-content-center gap-1">';
                    $buttons .= '<a href="' . $detailUrl . '" class="btn btn-info btn-sm text-white mb-2 d-flex align-items-center justify-content-center">Detail</a>';

                    if ($row->document_path) {
                        $buttons .= '<a href="' . Storage::url($row->document_path) . '" target="_blank" class="btn btn-sm btn-warning text-white mb-2">Lihat Dokumen</a>';
                    }

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['document', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $publicId, string $projectId)
    {
        $toolData = session('tool_data', null);
        if ($toolData === null) {
            abort(404);
        }
        $posisi_ct_id = $toolData['posisi_ct_id'];
        $tool_id = $toolData['tool_id'] ?? null;

        return view('pages.pengurangan_tool.create', compact(
            'toolData',
            'publicId',
            'projectId',
            'tool_id',
            'posisi_ct_id',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'public_id_detail_kerusakan_tool' => 'required|exists:detail_kerusakan_tools,public_id',
            'project_id' => 'required|exists:projects,project_id',
            'posisi_ct_id' => 'required|exists:posisi__c_t_s,id',
            'tool_id' => 'required|exists:tools,id',
            'tools' => 'required|array|min:1',
            'tools.*.kode_material' => 'required|exists:tools,kode_material',
            'tools.*.qty_dikurangi' => 'required|integer|min:1',
            'tools.*.alasan' => 'nullable|string|max:1000',
            'tools.*.document' => 'required|file|mimes:pdf,jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $uploadedDocuments = [];
        $validated = $validator->validated();
        DB::beginTransaction();
        $penguranganTool = null;
        try {
            foreach ($request->tools as $index => $toolData) {
                $tool = Tools::where('id', $validated['tool_id'])
                    ->where('posisi_ct_id', $validated['posisi_ct_id'])
                    ->whereHas(
                        'stockProject',
                        fn($q) => $q->where('projects.project_id', $request->project_id)
                    )
                    ->with([
                        'stockProject' => fn($q) => $q->where('projects.project_id', $request->project_id),
                    ])
                    ->lockForUpdate()
                    ->firstOrFail();
                $pivot = $tool->stockProject->first()->pivot;
                $toolQtyBefore = $pivot->qty;
                if (! $tool) {
                    throw new \Exception("Tool dengan kode material {$toolData['kode_material']} tidak ditemukan.");
                }

                $qtyToReduce = (int) $toolData['qty_dikurangi'];

                // sementara
                if ($qtyToReduce !== 1) {
                    throw new \Exception('Error');
                }

                if ($qtyToReduce > $toolQtyBefore) {
                    throw new \Exception("Jumlah ({$qtyToReduce}) yang akan dikurangi untuk tool {$tool->kode_material} melebihi stok yang tersedia ({$toolQtyBefore}).");
                }

                // Handle file upload for the current item
                $document = $request->file("tools.{$index}.document");
                $documentPath = $document->store('public/pengurangan_tools_documents');
                $uploadedDocuments[] = $documentPath; // Keep track of uploaded files for potential rollback
                $documentPathForDb = str_replace('public/', '', $documentPath);

                // Decrement tool quantity
                DB::table('tool_project_stocks')
                    ->where('tool_id', $tool->id)
                    ->where('project_id', $pivot->project_id)
                    ->decrement('qty', $qtyToReduce);

                $penguranganTool = PenguranganTool::create([
                    'tool_id' => $tool->id,
                    'user_id' => Auth::user()->id,
                    'qty_sebelum' => $toolQtyBefore,
                    'project_id' => $validated['project_id'],
                    'posisi_ct_id' => $validated['posisi_ct_id'],
                    'qty_dikurangi' => $qtyToReduce,
                    'qty_sesudah' => $toolQtyBefore - $qtyToReduce,
                    'alasan' => $toolData['alasan'] ?? null,
                    'document_path' => $documentPathForDb,
                    'tanggal_pengurangan' => now(),
                ]);
            }
            // ubah status kerusakan menjadi tidak bisa di perbaiki
            if ($request->has('public_id_detail_kerusakan_tool')) {
                $detailKerusakanTool = DetailKerusakanTool::where('public_id', $request->public_id_detail_kerusakan_tool)->firstOrFail();
                $detailKerusakanTool->status_perbaikan = 'tidak_bisa_diperbaiki';
                $detailKerusakanTool->save();
                // tambahkan id keruskan tool ke table pengurangan tools
                $penguranganTool->detail_kerusakan_tools_id = $detailKerusakanTool->id;
                $penguranganTool->save();
            }
            DB::commit();
            if ($request->has('public_id_detail_kerusakan_tool')) {
                return redirect()->route('tool-rusak.index')->with('success', 'Pengurangan stok tool berhasil dicatat.');
            }

            return redirect()->route('pengurangan-tool.index')->with('success', 'Pengurangan stok tool berhasil dicatat.');
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($uploadedDocuments as $path) {
                Storage::delete($path);
            }
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PenguranganTool $penguranganTool)
    {
        $penguranganTool->load([
            'tool',
            'user',
            'detailKerusakanTool.detailDaftarDistribusiTool.daftarDistribusiTool.distribusiTool',
        ]);

        return view('pages.pengurangan_tool.show', compact('penguranganTool'));
    }

    /**
     * Get tool details via AJAX.
     */
    public function getToolDetails(string $kode_material)
    {
        $tool = Tools::where('kode_material', $kode_material)->first();

        if ($tool) {
            return response()->json([
                'deskripsi' => $tool->deskripsi,
                'spesifikasi' => $tool->spesifikasi,
                'qty' => $tool->qty,
                'satuan' => $tool->satuan,
            ]);
        }

        return response()->json(['message' => 'Tool not found'], 404);
    }
}
