<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormStorePurchaseRequisitionRequest;
use App\Models\DetailKerusakanTool;
use App\Models\PurchaseRequisition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseRequisitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin|viewer');
    }
    public function index()
    {
        return view('pages.pr.index');
    }

    public function datatable(Request $request)
    {
        $query = PurchaseRequisition::withCount('details');
        if ($request->filled('status_filter')) {
            $query->where('status_pr', $request->status_filter);
        }

        if ($request->filled('tanggal_dari_filter')) {
            $query->whereDate('tanggal_pr', '>=', $request->tanggal_dari_filter);
        }

        if ($request->filled('tanggal_sampai_filter')) {
            $query->whereDate('tanggal_pr', '<=', $request->tanggal_sampai_filter);
        }

        if ($request->filled('no_pr_filter')) {
            $query->where('no_pr', 'like', '%' . $request->no_pr_filter . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal_pr', function ($row) {
                return Carbon::parse($row->tanggal_pr)->format('d/m/Y');
            })
            ->editColumn('status_pr', function ($row) {
                $badgeClass = $row->status_pr == 'sudah_datang' ? 'bg-success' : 'bg-warning';
                $statusText = $row->status_pr == 'sudah_datang' ? 'Sudah Datang' : 'Belum Datang';

                return '<span class="badge ' . $badgeClass . '">' . $statusText . '</span>';
            })
            ->addColumn('details_count', function ($row) {
                return '<span class="badge bg-info">' . ($row->details_count) . ' Items</span>';
            })
            ->addColumn('action', function ($row) {
                // $showUrl = route('pr.show', $row->public_id);
                // $prSudahDatangId = $row->public_id;
                // $prSudahDiCt = $row->public_id;
                // $eyeIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-fullscreen"></use></svg>';
                // $button = '<a href="' . $showUrl . '" class="btn btn-info btn-sm text-white">' . $eyeIcon . '</a>';
                // $prSudahDatangButton = '<button class="btn btn-success btn-sm text-white ms-1 sudah-datang-btn" data-id="' . $prSudahDatangId . '" data-no-pr="' . $row->no_pr . '">Sudah Datang</button>';
                // return $button . $prSudahDatangButton;
                $showUrl = route('pr.show', $row->public_id);
                $prSudahDatangId = $row->public_id;
                $prSudahDiCt = $row->public_id;
                $eyeIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-fullscreen"></use></svg>';
                $button = '<a href="' . $showUrl . '" class="btn btn-info btn-sm text-white">' . $eyeIcon . '</a>';
                $actionButton = '';
                if ($row->status_pr == 'belum_datang') {
                    $actionButton = '<button class="btn btn-success btn-sm text-white ms-1 sudah-datang-btn" data-id="' . $prSudahDatangId . '" data-no-pr="' . $row->no_pr . '">Sudah Datang</button>';
                }

                // } else {
                //     $actionButton = '<button class="btn btn-warning btn-sm text-white ms-1 sudah-di-ct-btn" data-id="' . $prSudahDiCt . '" data-no-pr="' . $row->no_pr . '">Sudah di CT</button>';
                // }
                return $button . $actionButton;
            })
            ->rawColumns(['status_pr', 'action', 'details_count'])
            ->make(true);
    }

    public function create()
    {
        // Ambil tools rusak yang belum masuk PR dan status perbaikan 'tidak_bisa_diperbaiki'
        $availableTools = DetailKerusakanTool::with([
            'detailDaftarDistribusiTool.daftarDistribusiTool.tool',
            'detailDaftarDistribusiTool.daftarDistribusiTool.tool.posisiCT',
            'detailDaftarDistribusiTool.daftarDistribusiTool.distribusiTool.project',
        ])
            ->where('status_perbaikan', 'tidak_bisa_diperbaiki')
            ->whereDoesntHave('prDetail') // Belum masuk PR
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.pr.create-pr', compact('availableTools'));
    }

    public function store(FormStorePurchaseRequisitionRequest $request)
    {
        $validated = $request->validated();
        try {
            DB::beginTransaction();
            $pr = PurchaseRequisition::create([
                'user_id' => auth()->user()->id,
                'no_pr' => $validated['no_pr'],
                'tanggal_pr' => $validated['tanggal_pr'],
            ]);
            // looping detail_kerusakan_tools_ids
            $detailKerusakanId = [];
            foreach ($validated['detail_kerusakan_tools_ids'] as $detailKerusakanToolId) {
                $detailKerusakanId[] = [
                    'detail_kerusakan_tools_id' => $detailKerusakanToolId,
                ];
            }
            $pr->details()->createMany($detailKerusakanId);
            DB::commit();

            return redirect()->route('pr.index')->with('success', 'Purchase Requisition berhasil diinput');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function show(PurchaseRequisition $purchaseRequisition)
    {
        $purchaseRequisition->load([
            'details.detailKerusakanTool.detailDaftarDistribusiTool.daftarDistribusiTool.tool',
            'details.detailKerusakanTool.detailDaftarDistribusiTool.daftarDistribusiTool.distribusiTool.bagian',
        ]);

        return view('pages.pr.show', compact('purchaseRequisition'));
    }

    public function sudahDatang(PurchaseRequisition $purchaseRequisition)
    {
        try {
            DB::beginTransaction();
            $purchaseRequisition->load(['details', 'details.detailKerusakanTool.detailDaftarDistribusiTool.daftarDistribusiTool.tool']);
            $purchaseRequisition->update(['status_pr' => 'sudah_datang']);
            // update keterangan di detail kerusakan tool menjadi 'sudah_diganti'
            foreach ($purchaseRequisition->details as $detail) {
                $detail->detailKerusakanTool->update(['keterangan' => 'sudah_diganti']);
            }
            //kemudian ?
            DB::commit();

            return redirect()->route('pr.index')->with('success', 'Status Purchase Requisition berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function sudahDatangCt(PurchaseRequisition $purchaseRequisition)
    {
        try {
            DB::beginTransaction();
            $purchaseRequisition->load(['details', 'details.detailKerusakanTool.detailDaftarDistribusiTool.daftarDistribusiTool.tool']);
            DB::commit();

            return redirect()->route('pr.index')->with('success', 'Status Purchase Requisition berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
