<?php

namespace App\Http\Controllers;

use App\Exports\DistribusiToolExport;
use App\Models\Bagian;
use App\Models\DaftarDistribusiTool;
use App\Models\Departemen;
use App\Models\DistribusiTool;
use App\Models\Project;
use App\Models\Tools;
use App\Models\Unit;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DistribusiController extends Controller
{
    public function index()
    {
        $departemens = Departemen::orderBy('nama_departemen', 'asc')->get();
        $bagians = Bagian::orderBy('nama_bagian', 'asc')->get();
        $units = Unit::orderBy('nama_unit', 'asc')->get();
        $projects = Project::all();

        return view('pages.distribusi_tool.index', compact('departemens', 'bagians', 'units', 'projects'));
    }

    public function datatable(Request $request)
    {
        if ($request->ajax()) {
            $query = DaftarDistribusiTool::with([
                'tool:id,kode_material,spesifikasi,deskripsi,satuan,jenis_material',
                'distribusiTool.departemen',
                'distribusiTool.user:id,name',
                'distribusiTool.bagian',
                'distribusiTool.unit',
                'distribusiTool.project'
            ])
                ->join(
                    'distribusi_tools',
                    'distribusi_tools.id',
                    '=',
                    'daftar_distribusi_tools.distribusi_tools_id'
                )
                ->select([
                    'daftar_distribusi_tools.*',
                    DB::raw('
            ROW_NUMBER() OVER (
                PARTITION BY daftar_distribusi_tools.distribusi_tools_id
                ORDER BY daftar_distribusi_tools.id
            ) as rn
        ')
                ]);

            // Filter berdasarkan tipe
            if ($request->filled('type')) {
                $query->whereHas('distribusiTool', function ($q) use ($request) {
                    $q->where('type', $request->type);
                });
            }
            // Filter berdasarkan kode material
            if ($request->filled('kode_material')) {
                $query->whereHas('tool', function ($q) use ($request) {
                    $q->where('kode_material', $request->kode_material);
                });
            }

            // Filter berdasarkan departemen
            if ($request->filled('departemen_id')) {
                $query->whereHas('distribusiTool', function ($q) use ($request) {
                    $q->where('departemen_id', $request->departemen_id);
                });
            }
            // Filter berdasarkan project
            if ($request->filled('project_id')) {
                $query->whereHas('distribusiTool', function ($q) use ($request) {
                    $q->where('project_id', $request->project_id);
                });
            }

            // Filter berdasarkan bagian
            if ($request->filled('bagian_id')) {
                $query->whereHas('distribusiTool', function ($q) use ($request) {
                    $q->where('bagian_id', $request->bagian_id);
                });
            }

            // Filter berdasarkan unit
            if ($request->filled('unit_id')) {
                $query->whereHas('distribusiTool', function ($q) use ($request) {
                    $q->where('unit_id', $request->unit_id);
                });
            }

            // Filter berdasarkan rentang tanggal
            if ($request->filled('start_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->whereHas('distribusiTool', function ($q) use ($startDate) {
                    $q->where('tanggal_pinjam_diserahkan', '>=', $startDate);
                });
            }
            if ($request->filled('end_date')) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereHas('distribusiTool', function ($q) use ($endDate) {
                    $q->where('tanggal_pinjam_diserahkan', '<=', $endDate);
                });
            }
            if ($request->filled('kategori')) {
                $query->whereHas('distribusiTool', function ($q) use ($request) {
                    $q->where('kategori', $request->kategori);
                });
            }
            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return ucfirst($row->distribusiTool->type);
                })
                ->addColumn('staff', function ($row) {
                    return $row->distribusiTool->user->name ?? '-';
                })
                ->addColumn('kode_material', function ($row) {
                    return $row->tool->kode_material;
                })
                ->addColumn('deskripsi', function ($row) {
                    return $row->tool->deskripsi;
                })
                ->addColumn('spesifikasi', function ($row) {
                    return $row->tool->spesifikasi;
                })
                ->addColumn('satuan', function ($row) {
                    return $row->tool->satuan;
                })
                ->addColumn('nama_peminjam', function ($row) {
                    return $row->distribusiTool->nama_peminjam;
                })
                ->addColumn('nip', function ($row) {
                    return $row->distribusiTool->nip;
                })
                ->addColumn('kategori', function ($row) {
                    if ($row->distribusiTool->kategori === null) {
                        return '-';
                    }
                    $kategori = strtoupper($row->distribusiTool->kategori);
                    $badgeClass = match ($kategori) {
                        'TOOL' => 'bg-warning',
                        'CONSUMABLE' => 'bg-primary',
                        'APD' => 'bg-info',
                        default => 'bg-secondary',
                    };
                    return '<span class="badge ' . $badgeClass . '">' . $kategori . '</span>';
                })
                ->editColumn('project', function ($row) {
                    return $row->distribusiTool->project->nama_project;
                })
                ->editColumn('jumlah_distribusi', function ($row) {
                    return $row->jumlah_distribusi_formatted;
                })
                ->addColumn('departemen', function ($row) {
                    return $row->distribusiTool->departemen->nama_departemen ?? '-';
                })
                ->addColumn('bagian', function ($row) {
                    return $row->distribusiTool->bagian->nama_bagian ?? '-';
                })
                ->addColumn('unit', function ($row) {
                    return $row->distribusiTool->unit->nama_unit ?? '-';
                })
                ->addColumn('tanggal_kembali', function ($row) {
                    $row->distribusiTool->tanggal_kembali ? $row->distribusiTool->tanggal_kembali->format('d M Y H:i') : '-';
                })
                ->editColumn('tanggal_pinjam_diserahkan', function ($row) {
                    return $row->distribusiTool->tanggal_pinjam_diserahkan->format('d M Y H:i');
                })
                ->editColumn('status', function ($row) {
                    if ($row->distribusiTool->status === 'kembali') {
                        return '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Kembali</span>';
                    } elseif ($row->distribusiTool->status === 'kembali_sebagian') {
                        return '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle"></i> Kembali Sebagian</span>';
                    } elseif ($row->distribusiTool->status === 'diserahkan') {
                        return '<span class="badge bg-primary"><i class="fas fa-hand-paper"></i> Diserahkan</span>';
                    } elseif ($row->distribusiTool->status === 'belum_kembali') {
                        return '<span class="badge bg-danger"><i class="fas fa-times-circle"></i> Belum Kembali</span>';
                    } elseif ($row->distribusiTool->status === 'batal') {
                        return '<span class="badge bg-danger"><i class="fas fa-times-circle"></i> Dibatalkan</span>';
                    } else {
                        return '<span class="badge bg-secondary">' . $row->distribusiTool->status . '</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $detailUrl  = route('distribusi.show', $row->distribusiTool->public_id);
                    $kembaliUrl = route('distribusi.peminjaman.kembali', $row->distribusiTool->public_id);
                    $baUrl  = route('distribusi.berita-acara', $row->distribusiTool->public_id);
                    $cancelUrl = route('distribusi.batalkan-penyerahan', $row->distribusiTool->public_id);
                    $cancelIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-x-circle"></use></svg>';
                    $printIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-print"></use></svg>';
                    $btn  = '<div class="d-flex justify-content-center gap-2">';
                    $within8Hours = Carbon::now()->diffInHours($row->distribusiTool->tanggal_pinjam_diserahkan) < 8;
                    $btn .= '<a href="' . $detailUrl . '" class="btn btn-info btn-sm text-white">
            <i class="fas fa-eye"></i> Detail
         </a>';
                    if ($row->distribusiTool->type === 'peminjaman' && $row->distribusiTool->status !== 'kembali') {
                        $btn .= '<a href="' . $kembaliUrl . '" class="btn btn-warning btn-sm">
                <i class="fas fa-undo"></i> Kembalikan
             </a>';
                    }
                    if ($row->distribusiTool->kategori === 'tool') {
                        $btn .= '<a href="' . $baUrl . '" class="btn btn-warning btn-sm text-white" target="_blank">
                ' . $printIcon . '
             </a>';
                    }
                    // upload bast
                    if (
                        $row->rn == 1 &&
                        $row->distribusiTool->type === 'penyerahan' &&
                        $row->distribusiTool->kategori === 'tool' &&
                        $row->distribusiTool->status !== 'batal' &&
                        is_null($row->distribusiTool->file_bast)
                    ) {
                        $btn .= '
            <button
                class="btn btn-primary btn-sm btn-upload-bast"
                data-coreui-toggle="modal"
                data-coreui-target="#modalUploadBast"
                data-id="' . $row->distribusi_tools_id . '">
                Upload BAST
            </button>
        ';
                    }
                    if ($row->distribusiTool->file_bast && $row->rn == 1) {
                        $btn .= '
            <a href="' . route('distribusi.unduh-bast-ttd', $row->distribusiTool->public_id) . '"
               class="btn btn-success btn-sm text-white"
               target="_blank"
               title="Download BAST">
              Download BAST
            </a>
        ';
                    }
                    if (Auth::check() && Auth::user()->hasRole('admin') && $within8Hours && $row->distribusiTool->type === 'penyerahan' && $row->distribusiTool->status !== 'batal') {
                        $btn .= '
        <form action="' . $cancelUrl . '" method="POST" onsubmit="return confirm(\'Yakin ingin membatalkan?\')" style="display:inline;">
            ' . csrf_field() . '
            ' . method_field('POST') . '
            <button type="submit" class="btn btn-danger text-white">
                ' . $cancelIcon . '
            </button>
        </form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'action', 'kategori'])
                ->make(true);
        }
    }

    /**
     * Melakukan ekspor data ke file Excel.
     */
    public function exportExcel(Request $request)
    {
        $filters = $request->only(['type', 'project_id', 'departemen_id', 'bagian_id', 'unit_id', 'start_date', 'end_date', 'kode_material', 'kategori']);
        $time = Carbon::now()->format('Y-m-d_H-i-s');
        return Excel::download(new DistribusiToolExport($filters), "{$time}_laporan_distribusi_tool.xlsx");
    }

    public function createPenyerahan()
    {
        return view('pages.distribusi_tool.create_penyerahan');
    }

    public function createPeminjaman()
    {
        return view('pages.distribusi_tool.create_peminjaman');
    }

    public function showPengembalian(DistribusiTool $distribusiTool)
    {
        $distribusiTool->load(['departemen', 'bagian', 'daftarDistribusiTool.tool', 'daftarDistribusiTool.posisiCt', 'project']);

        return view('pages.distribusi_tool.show_pengembalian', [
            'distribusi' => $distribusiTool,
        ]);
    }

    public function storePengembalian(Request $request, DistribusiTool $distribusiTool)
    {
        // return $request->all();
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:daftar_distribusi_tools,id',
            'items.*.posisi_ct_id' => 'required|exists:posisi__c_t_s,id',
            'items.*.qty_kembali' => 'required|integer|min:0',
            'details' => 'required|array',
            'details.*.jumlah_baik' => 'required|integer|min:0',
            'details.*.jumlah_rusak' => 'required|integer|min:0',
            'kerusakan_details' => 'array',
            'kerusakan_details.*' => 'array',
            'kerusakan_details.*.*' => 'required|string|max:500',
        ]);
        $validator->after(function ($validator) use ($request) {
            //menambahkan validasi, minimal nilai di datanya ada yang di isi
            foreach ($request->items as $key => $item) {
                $qtyKembali = (int) $item['qty_kembali'];
                $toolId = (string) $item['id'];
                if ($qtyKembali === 0) {
                    continue;
                }
                if (!isset($request->details[$toolId])) {
                    continue;
                }
                $baik = (int) ($request->details[$toolId]['jumlah_baik'] ?? 0);
                $rusak = (int) ($request->details[$toolId]['jumlah_rusak'] ?? 0);
                if ($baik === 0 && $rusak === 0) {
                    $validator->errors()->add(
                        "details.$key.jumlah_baik",
                        'Jumlah baik atau rusak harus lebih dari 0'
                    );
                }
            }
        });
        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validasi gagal.',
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        DB::beginTransaction();
        try {
            $distribusiTool->load(['daftarDistribusiTool', 'project']);
            $all_items_returned = true;

            foreach ($request->items as $itemData) {
                if ($itemData['qty_kembali'] == 0) {
                    continue;
                }

                $daftarDistribusiTool = DaftarDistribusiTool::where('id', $itemData['id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($daftarDistribusiTool->distribusi_tools_id != $distribusiTool->id) {
                    throw new \Exception('Item tidak terkait dengan peminjaman ini.');
                }

                $remaining_qty = $daftarDistribusiTool->jumlah_distribusi - ($daftarDistribusiTool->qty_kembali ?? 0);
                if ($itemData['qty_kembali'] > $remaining_qty) {
                    throw new \Exception('Jumlah yang dikembalikan untuk ' . $daftarDistribusiTool->tool->deskripsi . ' melebihi jumlah yang belum dikembalikan.');
                }

                $details = $request->details[$daftarDistribusiTool->id];
                $jumlahBaik = $details['jumlah_baik'];
                $jumlahRusak = $details['jumlah_rusak'];

                if (($jumlahBaik + $jumlahRusak) != $itemData['qty_kembali']) {
                    throw new \Exception('Total kondisi (Baik + Rusak) tidak cocok dengan Qty Kembali untuk ' . $daftarDistribusiTool->tool->deskripsi);
                }
                $tool = Tools::where('id', $daftarDistribusiTool->tool_id)
                    ->where('posisi_ct_id', $itemData['posisi_ct_id'])
                    ->whereHas(
                        'stockProject',
                        fn($q) => $q->where('projects.project_id', $distribusiTool->project_id)
                    )
                    ->firstOrFail();
                // tool rusak tetap di hitung sebagai stok tools
                $total = $jumlahBaik + $jumlahRusak;
                DB::table('tool_project_stocks')
                    ->where('tool_id', $tool->id)
                    ->where('posisi_ct_id', $itemData['posisi_ct_id'])
                    ->where('project_id', $distribusiTool->project_id)
                    ->increment('qty', $total);

                if ($jumlahBaik > 0) {
                    $daftarDistribusiTool->details()->create([
                        'jumlah' => $jumlahBaik,
                        'kondisi' => 'baik',
                        'tanggal_kembali' => now(),
                    ]);
                }

                if ($jumlahRusak > 0) {
                    $detailDaftarDistribusiToolRusak = $daftarDistribusiTool->details()->create([
                        'jumlah' => $jumlahRusak,
                        'kondisi' => 'rusak',
                        'tanggal_kembali' => now(),
                    ]);

                    // menyimpan deskripsi detail kerusakan pada setiap item di detail daftar distribusi tools
                    if (isset($request->kerusakan_details[$daftarDistribusiTool->id]) && is_array($request->kerusakan_details[$daftarDistribusiTool->id])) {
                        foreach ($request->kerusakan_details[$daftarDistribusiTool->id] as $deskripsiKerusakan) {
                            $detailDaftarDistribusiToolRusak->detailKerusakanTools()->create([
                                'public_id' => Str::uuid(),
                                'deskripsi_kerusakan' => $deskripsiKerusakan,
                                'status_pengajuan_spr' => 'belum',
                            ]);
                        }
                    } else {
                        throw new \Exception('Detail kerusakan wajib diisi untuk setiap tool yang rusak.');
                    }
                }

                $daftarDistribusiTool->increment('qty_kembali', $itemData['qty_kembali']);
                $daftarDistribusiTool->tanggal_kembali = now();
                $daftarDistribusiTool->refresh();

                if ($daftarDistribusiTool->qty_kembali >= $daftarDistribusiTool->jumlah_distribusi) {
                    $daftarDistribusiTool->status = 'dikembalikan';
                } else {
                    $all_items_returned = false;
                }
                $daftarDistribusiTool->save();
            }

            // --- Update Status DistribusiTool ---
            $distribusiTool->load('daftarDistribusiTool'); // Reload relation

            $isFullyReturned = $distribusiTool->daftarDistribusiTool->every(fn($item) => $item->status === 'dikembalikan');

            if ($isFullyReturned) {
                $distribusiTool->status = 'kembali';
                $distribusiTool->tanggal_kembali = now();
            } else {
                $distribusiTool->status = 'kembali_sebagian';
            }
            $distribusiTool->save();
            DB::commit();
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pengembalian tool berhasil diproses.']);
            }
            return redirect()->route('distribusi.index')->with('success', 'Pengembalian tool berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                ], 409);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(DistribusiTool $distribusiTool)
    {
        $distribusiTool->load(['project', 'projectDest', 'departemen', 'bagian', 'unit', 'daftarDistribusiTool.tool', 'daftarDistribusiTool.details', 'daftarDistribusiTool.details.detailKerusakanTools']);

        return view('pages.distribusi_tool.show', [
            'distribusi' => $distribusiTool,
        ]);
    }

    public function getBagian($departemen_id)
    {
        $bagian = Bagian::where('departemen_id', $departemen_id)->get();

        return response()->json($bagian);
    }

    public function getUnit($bagian_id)
    {
        $unit = Unit::where('bagian_id', $bagian_id)->get();

        return response()->json($unit);
    }
    public function batalkanPenyerahan(Request $request, DistribusiTool $distribusiTool)
    {
        $distribusiTool->load(['daftarDistribusiTool.tool', 'project']);
        if ($distribusiTool->status == 'batal') {
            abort(400, 'Distribusi tool sudah dibatalkan.');
        }
        try {
            DB::beginTransaction();
            //ubah status distribusi tool
            $distribusiTool->status = 'batal';
            $distribusiTool->save();
            foreach ($distribusiTool->daftarDistribusiTool as $key => $value) {
                // kembalikan jumlah stok ke project
                $jumlahDistribusi = $value->jumlah_distribusi;
                DB::table('tool_project_stocks')
                    ->where('tool_id', $value->tool_id)
                    ->where('project_id', $distribusiTool->project_id)
                    ->where('posisi_ct_id', $value->posisi_ct_id)
                    ->increment('qty', $jumlahDistribusi);
            }
            DB::commit();
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pembatalan Berhasil Di Proses.']);
            }
            return redirect()->route('distribusi.index')->with('success', 'Pembatalan Berhasil Di Proses.');
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Pembatalan Gagal Di Proses.',
                ], 409);
            }
            return redirect()->route('distribusi.index')->with('error', 'Pembatalan Gagal Di Proses.');
        }
    }
    public function beritaAcara(DistribusiTool $distribusiTool)
    {
        $distribusiTool->load(['daftarDistribusiTool.tool', 'project', 'departemen', 'bagian', 'unit']);
        $kodeBagian = DB::table('kode_bagian_spm')
            ->join('departemens', 'kode_bagian_spm.departemen_id', '=', 'departemens.id')
            ->join('bagians', 'kode_bagian_spm.bagian_id', '=', 'bagians.id')
            ->join('units', 'kode_bagian_spm.unit_id', '=', 'units.id')
            ->first();
        $pdf = Pdf::loadView('pages.distribusi_tool.berita-acara', compact('distribusiTool', 'kodeBagian'));
        return $pdf->stream('berita-acara.pdf');
    }
    public function uploadBast(Request $request)
    {
        $validated = $request->validate([
            'distribusi_tool_id' => [
                'required',
                'integer',
                'exists:distribusi_tools,id'
            ],
            'file_bast' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ]);
        try {
            $distribusi = DistribusiTool::findOrFail($validated['distribusi_tool_id']);
            if ($distribusi->type !== 'penyerahan') {
                return back()->withErrors([
                    'file_bast' => 'BAST hanya dapat diupload untuk penyerahan.'
                ]);
            }
            if ($distribusi->file_bast && Storage::disk('public')->exists($distribusi->file_bast)) {
                Storage::disk('public')->delete($distribusi->file_bast);
            }
            $path = $request->file('file_bast')->store(
                'bast',
                'public'
            );
            $distribusi->update([
                'file_bast' => $path,
            ]);
            return redirect()
                ->back()
                ->with('success', 'File BAST berhasil diupload.');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return back()->withErrors([
                'file_bast' => 'File BAST gagal diupload.'
            ]);
        }
    }
    public function downloadBastFile(DistribusiTool $distribusiTool)
    {
        if ($distribusiTool->file_bast && Storage::disk('public')->exists($distribusiTool->file_bast)) {
            return Storage::disk('public')->response($distribusiTool->file_bast);
        }
        return redirect()->route('distribusi.index')->with('error', 'File BAST tidak ditemukan.');
    }
}
