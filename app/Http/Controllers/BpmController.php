<?php

namespace App\Http\Controllers;

use App\Models\Bpm;
use App\Models\CuttingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BpmController extends Controller
{
    public function index()
    {
        return view('bon-permintaan-material.index');
    }

    public function create(Request $request)
    {
        $selectedId = $request->get('cutting_plan_id');

        $cuttingPlans = CuttingPlan::orderBy('no_cp')
            ->when($selectedId, fn ($q) => $q->where('id', $selectedId))
            ->get(['id', 'no_cp']);

        return view('bon-permintaan-material.create', compact(
            'cuttingPlans', 'selectedId'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cutting_plan_id' => 'required|exists:cutting_plans,id',
            'bpm_no'          => 'required|string|max:255',
            'specification'   => 'nullable|string',
            'total'           => 'nullable|numeric',
            'unit'            => 'nullable|string|max:50',
            'request_date'    => 'nullable|date',
            'bprm_no'         => 'nullable|string|max:255',
            'brpm_date'       => 'nullable|date',
            'bprm_total'      => 'nullable|numeric',
            'stock'           => 'nullable|string|max:255',
        ], [
            'cutting_plan_id.required' => 'Cutting Plan wajib dipilih.',
            'bpm_no.required'          => 'No BPM wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cuttingPlanId = $validator->validated()['cutting_plan_id'];
        $bpmNo = $validator->validated()['bpm_no'];

        // Find existing BPM by bpm_no or create new
        $bpm = Bpm::where('bpm_no', $bpmNo)->first();
        if (!$bpm) {
            $bpm = Bpm::create(array_merge($validator->validated(), [
                'user_id' => Auth::id(),
            ]));
        }

        // Link Cutting Plan to BPM
        CuttingPlan::where('id', $cuttingPlanId)->update(['bpm_id' => $bpm->id]);

        return response()->json([
            'success' => true,
            'message' => 'Bon Permintaan Material berhasil disimpan.',
            'redirect' => route('bon-permintaan-material.index'),
        ]);
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $perPage = (int) $request->get('per_page', 10);
        $rows = $this->baseQuery($request)
            ->orderBy('bpms.created_at', 'desc')
            ->paginate($perPage);

        return response()->json($rows);
    }

    private function baseQuery(Request $request)
    {
        $query = Bpm::query()
            ->with('cuttingPlans')
            ->select('bpms.*');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bpms.bpm_no', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.specification', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.total', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.unit', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.bprm_no', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.stock', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    private function exportColumns()
    {
        return [
            'bpm_no'        => 'No BPM',
            'specification' => 'Keterangan',
            'total'         => 'Jumlah',
            'unit'          => 'Unit',
            'request_date'  => 'Tanggal Permintaan',
            'bprm_no'       => 'Nomor BPRM',
            'brpm_date'     => 'Tanggal BPRM',
            'bprm_total'    => 'Jumlah BPRM',
            'stock'         => 'Stok',
        ];
    }

    private function formatExportValue($row, $key)
    {
        switch ($key) {
            case 'request_date':
            case 'brpm_date':
                return $row->$key
                    ? \Carbon\Carbon::parse($row->$key)->translatedFormat('d F Y')
                    : '';
            default:
                return $row->$key ?? '';
        }
    }

    public function export(Request $request)
    {
        $allowed = $this->exportColumns();
        $columns = array_values(array_filter(
            (array) $request->input('columns', []),
            fn ($c) => isset($allowed[$c])
        ));

        if (empty($columns)) {
            $columns = array_keys($allowed);
        }

        $rows = $this->baseQuery($request)
            ->orderBy('bpms.created_at', 'desc')
            ->get()
            ->map(function ($row) use ($columns) {
                $data = [];
                foreach ($columns as $key) {
                    $data[$key] = $this->formatExportValue($row, $key);
                }
                return $data;
            });

        $headings = array_map(fn ($c) => $allowed[$c], $columns);

        $export = new \App\Exports\CuttingPlanExport($headings, $rows);

        return Excel::download($export, 'bon-permintaan-material.xlsx');
    }

    public function destroy(Bpm $bpm)
    {
        // Unlink all cutting plans from this BPM
        CuttingPlan::where('bpm_id', $bpm->id)->update(['bpm_id' => null]);
        $bpm->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_values(array_filter(
            (array) $request->input('ids', []),
            fn ($id) => is_string($id) || is_numeric($id)
        ));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih.'], 422);
        }

        // Unlink all cutting plans from these BPMs
        CuttingPlan::whereIn('bpm_id', $ids)->update(['bpm_id' => null]);
        Bpm::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Data terpilih berhasil dihapus.']);
    }
}
