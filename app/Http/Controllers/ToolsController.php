<?php

namespace App\Http\Controllers;

use App\Models\DaftarKodeMaterial;
use App\Models\DetailKerusakanTool;
use App\Models\Posisi_CT;
use App\Models\Project;
use App\Models\ToolProjectStock;
use App\Models\Tools;
use App\Models\UpdateStockTool;
use ErrorException;
use App\Exports\ToolsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ToolsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posisiCTs = Posisi_CT::orderBy('nama_posisi_ct')->get();
        $projects = Project::whereNull('closed_at')->get();

        return view('pages.tools.index', compact('posisiCTs', 'projects'));
    }

    /**
     * Get tools data for DataTables
     */
    public function getDataTools(Request $request)
    {
        if ($request->ajax()) {

            $query = ToolProjectStock::whereHas('tool', function ($q) {
                $q->where('jenis_material', 'tool');
            })->whereHas('project', function ($q) {
                $q->whereNull('closed_at');
            })
                ->with(['tool', 'project', 'posisiCT']);
            if ($request->filled('kode_material')) {
                $query->whereHas('tool', function ($q) use ($request) {
                    $q->where('kode_material', 'like', '%' . $request->kode_material . '%');
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
                $query->where('posisi_ct_id', $request->posisi_ct_id);
            }
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
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
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Export tools data to Excel
     */
    public function export(Request $request)
    {
        if (! Auth::user()->hasRole(['admin'])) {
            abort(403);
        }

        $filters = $request->only(['kode_material', 'deskripsi', 'spesifikasi', 'posisi_ct_id', 'project_id']);
        $time = now()->format('Y-m-d_H-i-s');

        return Excel::download(new ToolsExport($filters), "{$time}_daftar_tools.xlsx");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $posisiCTs = Posisi_CT::orderBy('nama_posisi_ct')->get();
        $project = Project::withoutGlobalScope('onlyWithStock')->get();

        return view('pages.tools.create', [
            'posisiCTs' => $posisiCTs,
            'projects' => $project,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(?string $publicId = null, ?string $projectId = null, ?string $ctId = null)
    {
        $tool = Tools::where('public_id', $publicId)
            ->whereHas('stockProject', function ($q) use ($projectId, $ctId) {
                $q->where('projects.project_id', $projectId)
                    ->where('posisi_ct_id', $ctId);
            })
            ->with([
                'stockProject' => fn($q) => $q->where('projects.project_id', $projectId),
                'updateStockTools',
                'materialTransferPostingItems.posting.toProject'
            ])
            ->firstOrFail();
        $distribusiHistory = $tool->load([
            'daftarDistribusiTools.distribusiTool.unit',
            'daftarDistribusiTools.distribusiTool.bagian',
            'updateStockTools.user',
            'penguranganTools.user',
        ]);
        // ambil data kerusakan dari detail daftar distribusi tool
        $daftarDistribusiIds = $tool->daftarDistribusiTools()->pluck('id');
        $detailDaftarDistribusiIds = \App\Models\DetailDaftarDistribusiTool::whereIn('daftar_distribusi_tool_id', $daftarDistribusiIds)->pluck('id');
        $damageHistory = DetailKerusakanTool::whereIn('detail_daftar_distribusi_tools_id', $detailDaftarDistribusiIds)->latest()->get();

        return view('pages.consumable.show', [
            'tool' => $tool,
            'transferHistory' => $tool->materialTransferPostingItems,
            'updateHistory' => $tool->updateStockTools,
            'penguranganHistory' => $tool->penguranganTools,
            'distribusiHistory' => $distribusiHistory,
            'damageHistory' => $damageHistory,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tools $tool, Request $request)
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        if ($request->query('project_id') == null) {
            abort(403);
        }
        $material = $tool;
        $material->load(['stockProject' => function ($q) use ($material, $request) {
            $q->where('tool_project_stocks.posisi_ct_id', $material->posisi_ct_id);
            $q->where('tool_project_stocks.project_id', $request->query('project_id'));
        }]);
        $material->stockProject->first();
        $posisiCTs = Posisi_CT::orderBy('nama_posisi_ct')->get();
        return view('pages.tools.edit', compact('material', 'posisiCTs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tools $tool)
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $validated = $request->validate([
            'posisi_ct_id' => 'nullable|exists:posisi__c_t_s,id',
            'project_id' => 'required|exists:projects,project_id',
            'vendor' => 'nullable|in:vendor,INKA',
            'keterangan_vendor' => 'nullable|string|max:1000',
            'total_stok' => 'required',
            'jenis_material' => 'required|in:tool,consumable,apd',
        ]);

        try {
            DB::beginTransaction();
            $existTool = Tools::where('kode_material', $tool->kode_material)
                ->where('posisi_ct_id', $validated['posisi_ct_id'])->first();

            if ($existTool && $existTool->id !== $tool->id) {
                throw new ErrorException("Tool Dengan Kode Material " . $tool->kode_material . " Dan Posisi CT " . $existTool->posisiCT->nama_posisi_ct . " Sudah Ada");
            }
            $tool->update($validated);
            DB::table('tool_project_stocks')
                ->where('tool_id', $existTool->id)
                ->where('posisi_ct_id', $existTool->posisi_ct_id)
                ->where('project_id', $validated['project_id'])
                ->update(['qty' => $validated['total_stok']]);
            DB::commit();
            return redirect()->route('tool.index')->with('success', 'Berhasil diperbarui.');
        } catch (ErrorException $e) {
            DB::rollback();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withErrors(['error' => 'Gagal memperbarui tool: '])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tools $tool)
    {
        try {
            $tool->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data tool berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data tool: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search material by kode_material with comprehensive validation and response
     */
    public function searchMaterial(Request $request)
    {
        try {
            // Validate request parameters
            $request->validate([
                'kode_material' => 'required|string|max:255',
            ]);

            $kodeMaterial = $request->get('kode_material');

            // Sanitize input
            $kodeMaterial = trim($kodeMaterial);

            if (empty($kodeMaterial)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode material tidak boleh kosong',
                    'code' => 'EMPTY_MATERIAL_CODE',
                ], 400);
            }

            // First check if tool already exists in Tools table
            $existingTool = Tools::where('kode_material', $kodeMaterial)->first();

            if ($existingTool) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tool dengan kode material ini sudah ada. Gunakan fitur update stok untuk menambah quantity.',
                    'code' => 'TOOL_ALREADY_EXISTS',
                    'existing_tool' => [
                        'kode_material' => $existingTool->kode_material,
                        'deskripsi' => $existingTool->deskripsi,
                        'qty' => $existingTool->qty,
                        'satuan' => $existingTool->satuan,
                    ],
                ], 409);
            }

            // Search in DaftarKodeMaterial for new tool creation
            $material = \App\Models\DaftarKodeMaterial::where('kode_material', $kodeMaterial)->first();

            if (! $material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode material tidak ditemukan di master data',
                    'code' => 'MATERIAL_NOT_FOUND',
                ], 404);
            }

            // Return structured response for successful material lookup
            return response()->json([
                'success' => true,
                'mode' => 'create',
                'data' => [
                    'kode_material' => $material->kode_material,
                    'deskripsi' => $material->deskripsi,
                    'spesifikasi' => $material->spesifikasi,
                    'satuan' => $material->satuan,
                ],
                'message' => 'Material ditemukan dan siap untuk dibuat tool baru',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors(),
                'code' => 'VALIDATION_ERROR',
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in searchMaterial: ' . $e->getMessage(), [
                'kode_material' => $request->get('kode_material'),
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
    //     // Start database transaction for data consistency
    //     DB::beginTransaction();
    //     try {
    //         // Determine form mode based on existing tool
    //         $kodeMaterial = trim($request->input('kode_material'));
    //         $posisiCtId = $request->input('posisi_ct_id');
    //         $projectId = $request->input('project_id');

    //         $existingTool = Tools::where([
    //             'kode_material' => $kodeMaterial,
    //             'posisi_ct_id' => $posisiCtId,
    //         ])->first();

    //         $hasProjectStock = false;
    //         if ($existingTool) {
    //             $hasProjectStock = DB::table('tool_project_stocks')
    //                 ->where('tool_id', $existingTool->id)
    //                 ->where('posisi_ct_id', $posisiCtId)
    //                 ->where('project_id', $projectId)
    //                 ->exists();
    //         }

    //         $isUpdateMode = $existingTool && $hasProjectStock;

    //         $baseRules = [
    //             'kode_material' => 'required|string|max:255',
    //             'posisi_ct_id' => 'required|exists:posisi__c_t_s,id',
    //             'project_id' => 'required|exists:projects,project_id',
    //             'dokumen_pengambilan' => 'required|in:bpm,memo',
    //             'nomor_dokumen' => 'required|string|max:255',
    //             'dokumen_pengambilan_file' => 'required|file|mimes:pdf,jpeg,png,jpg|max:2048',
    //         ];
    //         // Handle file upload for dokumen_pengambilan_file
    //         $filePath = null;
    //         if ($request->hasFile('dokumen_pengambilan_file')) {
    //             $file = $request->file('dokumen_pengambilan_file');
    //             $nomorDokumen = preg_replace('/[^A-Za-z0-9\-]/', '-', $request->input('nomor_dokumen'));
    //             $nomorDokumen = preg_replace('/-+/', '-', $nomorDokumen);
    //             $filename = $nomorDokumen . '.' . $file->getClientOriginalExtension();
    //             $filePath = $file->storeAs('dokumen_pengambilan', $filename, 'public');
    //         } else {
    //             throw new \Exception('Dokumen pengambilan harus diupload');
    //         }


    //         if ($isUpdateMode) {
    //             // Update mode validation - only quantity and position required
    //             $validationRules = array_merge($baseRules, [
    //                 'keterangan_update' => 'nullable|string|max:1000',
    //                 'qty_ditambah' => 'required|integer|min:1',
    //             ]);
    //             $messages = [
    //                 'qty_ditambah.min' => 'Jumlah yang ditambahkan harus minimal 1',
    //             ];
    //         } else {
    //             // Create mode validation - all fields required for new tool
    //             $validationRules = array_merge($baseRules, [
    //                 'kode_material' => [
    //                     'required',
    //                     function ($attribute, $value, $fail) use ($request) {
    //                         $tool = Tools::where('kode_material', $value)
    //                             ->where('posisi_ct_id', $request->posisi_ct_id)
    //                             ->first();

    //                         if ($tool) {
    //                             $exists = DB::table('tool_project_stocks')
    //                                 ->where('tool_id', $tool->id)
    //                                 ->where('posisi_ct_id', $request->posisi_ct_id)
    //                                 ->where('project_id', $request->project_id)
    //                                 ->exists();

    //                             if ($exists) {
    //                                 $fail('Kode material sudah ada untuk posisi dan project tersebut.');
    //                             }
    //                         }
    //                     },
    //                 ],
    //                 'jenis_material' => 'required|in:tool,consumable,apd',
    //                 'vendor' => 'nullable|in:vendor,INKA',
    //                 'keterangan_vendor' => 'nullable|string|max:1000',
    //                 'qty' => 'required|integer|min:0',
    //             ]);
    //             $messages = [
    //                 'jenis_material.required' => 'Jenis material harus dipilih',
    //             ];
    //         }

    //         $validated = $request->validate($validationRules, $messages);

    //         if ($isUpdateMode) {
    //             // Update existing tool stock
    //             $currentStock = DB::table('tool_project_stocks')
    //                 ->where('tool_id', $existingTool->id)
    //                 ->where('posisi_ct_id', $posisiCtId)
    //                 ->where('project_id', $projectId)
    //                 ->first();

    //             $qtyBefore = $currentStock->qty;
    //             $qtyAdded = $validated['qty_ditambah'];

    //             // Update stock in tool_project_stocks
    //             DB::table('tool_project_stocks')
    //                 ->where('tool_id', $existingTool->id)
    //                 ->where('posisi_ct_id', $posisiCtId)
    //                 ->where('project_id', $projectId)
    //                 ->increment('qty', $qtyAdded);

    //             // Create audit trail for stock update
    //             $auditTrailUpdate = UpdateStockTool::create([
    //                 'user_id' => Auth::id(),
    //                 'tool_id' => $existingTool->id,
    //                 'project_id' => $projectId,
    //                 'qty_sebelum' => $qtyBefore,
    //                 'qty_update' => $qtyAdded,
    //                 'qty_sesudah' => $qtyBefore + $qtyAdded,
    //                 'type' => 'tambah_stok',
    //                 'dokumen_pengambilan' => $validated['dokumen_pengambilan'],
    //                 'nomor_dokumen' => $validated['nomor_dokumen'],
    //                 'dokumen_pengambilan_path' => $filePath,
    //                 'tanggal_update' => now(),
    //             ]);

    //             $auditTrailUpdate->updateStockTool()->create([
    //                 'project_id' => $projectId,
    //             ]);

    //             DB::commit();

    //             return redirect()->route('update-stok-tool.index')->with(
    //                 'success',
    //                 "Stok tool berhasil ditambahkan. Quantity berubah dari {$qtyBefore} menjadi " . ($qtyBefore + $qtyAdded)
    //             );
    //         } else {
    //             // Create new tool or add to existing tool for new project
    //             if (! $existingTool) {
    //                 // Verify material exists in master data
    //                 $material = \App\Models\DaftarKodeMaterial::where('kode_material', $kodeMaterial)->first();

    //                 if (! $material) {
    //                     throw new \Exception('Kode material tidak ditemukan di master data');
    //                 }

    //                 // Create new tool record
    //                 $newTool = Tools::create([
    //                     'kode_material' => $material->kode_material,
    //                     'deskripsi' => $material->deskripsi,
    //                     'spesifikasi' => $material->spesifikasi,
    //                     'satuan' => $material->satuan,
    //                     'jenis_material' => $validated['jenis_material'],
    //                     'vendor' => $validated['vendor'] ?? null,
    //                     'keterangan_vendor' => $validated['keterangan_vendor'] ?? null,
    //                     'posisi_ct_id' => $validated['posisi_ct_id'] ?? null,
    //                     'tanggal_ditambahkan' => now(),
    //                 ]);

    //                 $toolToUse = $newTool;
    //             } else {
    //                 $toolToUse = $existingTool;
    //             }

    //             // Insert into tool_project_stocks
    //             DB::table('tool_project_stocks')->insert([
    //                 'tool_id' => $toolToUse->id,
    //                 'posisi_ct_id' => $validated['posisi_ct_id'],
    //                 'project_id' => $validated['project_id'],
    //                 'qty' => (int) $validated['qty'],
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);

    //             // Create audit trail for tool input
    //             $auditTrailCreate = UpdateStockTool::create([
    //                 'user_id' => Auth::id(),
    //                 'tool_id' => $toolToUse->id,
    //                 'project_id' => $projectId,
    //                 'qty_sebelum' => 0,
    //                 'qty_update' => (int) $validated['qty'],
    //                 'qty_sesudah' => (int) $validated['qty'],
    //                 'type' => 'input_material',
    //                 'dokumen_pengambilan' => $validated['dokumen_pengambilan'],
    //                 'nomor_dokumen' => $validated['nomor_dokumen'],
    //                 'dokumen_pengambilan_path' => $filePath,
    //                 'tanggal_update' => now(),
    //             ]);

    //             $auditTrailCreate->updateStockTool()->create([
    //                 'project_id' => $projectId,
    //             ]);

    //             DB::commit();

    //             return redirect()->route('update-stok-tool.index')->with(
    //                 'success',
    //                 'Tool berhasil ditambahkan ke project dengan quantity ' . $validated['qty']
    //             );
    //         }
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         DB::rollBack();

    //         return back()->withErrors($e->errors())->withInput();
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return back()->withErrors([
    //             'error' => 'Terjadi kesalahan: ' . $e->getMessage(),
    //         ])->withInput();
    //     }
    // }

    // public function findTool(Request $request)
    // {
    //     $request->validate([
    //         'kode_material' => 'required|string',
    //         'posisi_ct_id' => 'required|integer|exists:posisi__c_t_s,id',
    //         'project_id' => 'required|string|exists:projects,project_id',
    //     ]);

    //     $kodeMaterial = $request->kode_material;
    //     $posisiCtId = $request->posisi_ct_id;
    //     $selectedProject = $request->project_id;

    //     $existingTool = Tools::where('kode_material', $kodeMaterial)
    //         ->where('posisi_ct_id', $posisiCtId)
    //         ->first();

    //     if ($existingTool) {
    //         // Check if tool has relation with the selected project in tool_project_stocks
    //         $hasProjectRelation = DB::table('tool_project_stocks')
    //             ->where('tool_id', $existingTool->id)
    //             ->where('posisi_ct_id', $posisiCtId)
    //             ->where('project_id', $selectedProject)
    //             ->first();

    //         if ($hasProjectRelation) {
    //             // Get stock data for selected project
    //             $stockProject = DB::table('tool_project_stocks')
    //                 ->join('projects', 'tool_project_stocks.project_id', '=', 'projects.project_id')
    //                 ->where('tool_project_stocks.tool_id', $existingTool->id)
    //                 ->where('tool_project_stocks.posisi_ct_id', $posisiCtId)
    //                 ->where('tool_project_stocks.project_id', $selectedProject)
    //                 ->select(
    //                     'projects.project_id',
    //                     'projects.nama_project',
    //                     'projects.created_at',
    //                     'projects.updated_at',
    //                     'tool_project_stocks.qty'
    //                 )
    //                 ->get()
    //                 ->map(function ($item) use ($existingTool, $posisiCtId) {
    //                     return [
    //                         'project_id' => $item->project_id,
    //                         'nama_project' => $item->nama_project,
    //                         'created_at' => $item->created_at,
    //                         'updated_at' => $item->updated_at,
    //                         'pivot' => [
    //                             'tool_id' => $existingTool->id,
    //                             'posisi_ct_id' => $posisiCtId,
    //                             'project_id' => $item->project_id,
    //                             'qty' => $item->qty,
    //                         ],
    //                     ];
    //                 });

    //             // Prepare tool data with filtered stock
    //             $toolData = [
    //                 'id' => $existingTool->id,
    //                 'public_id' => $existingTool->public_id,
    //                 'kode_material' => $existingTool->kode_material,
    //                 'deskripsi' => $existingTool->deskripsi,
    //                 'spesifikasi' => $existingTool->spesifikasi,
    //                 'satuan' => $existingTool->satuan,
    //                 'vendor' => $existingTool->vendor,
    //                 'keterangan_vendor' => $existingTool->keterangan_vendor,
    //                 'jenis_material' => $existingTool->jenis_material,
    //                 'tanggal_ditambahkan' => $existingTool->tanggal_ditambahkan,
    //                 'created_at' => $existingTool->created_at,
    //                 'updated_at' => $existingTool->updated_at,
    //                 'deleted_at' => $existingTool->deleted_at,
    //                 'posisi_ct_id' => $existingTool->posisi_ct_id,
    //                 'stock_project' => $stockProject,
    //             ];

    //             // Tool found with project relation, return update mode
    //             return response()->json([
    //                 'success' => true,
    //                 'mode' => 'update',
    //                 'data' => $toolData,
    //             ]);
    //         }
    //     }

    //     // Tool not found or no project relation, check master data for material info
    //     $material = DaftarKodeMaterial::where('kode_material', $kodeMaterial)->first();

    //     if ($material) {
    //         // Material found, return create mode with master data
    //         return response()->json([
    //             'success' => true,
    //             'mode' => 'create',
    //             'data' => [
    //                 'kode_material' => $material->kode_material,
    //                 'deskripsi' => $material->deskripsi,
    //                 'spesifikasi' => $material->spesifikasi,
    //                 'satuan' => $material->satuan,
    //             ],
    //         ]);
    //     }

    //     // Material not found in master data
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Kode material tidak ditemukan di master data.',
    //     ], 404);
    // }
}
