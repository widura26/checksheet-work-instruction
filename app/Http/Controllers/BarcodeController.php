<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\CuttingPlan;
use App\Models\Oka;
use App\Models\Part;
use App\Models\User;
use App\Models\WiTrn;
use App\Exports\CuttingPlanExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BarcodeController extends Controller
{
    public function index()
    {
        return view('barcode.index');
    }

    public function create(Request $request)
    {
        $cuttingPlans = CuttingPlan::orderBy('no_cp')->get(['id', 'no_cp']);
        $selectedId = $request->get('cutting_plan_id');

        return view('barcode.create', compact('cuttingPlans', 'selectedId'));
    }

    public function parts(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $cuttingPlanId = $request->get('cutting_plan_id');

        if (! $cuttingPlanId) {
            return response()->json(['data' => []]);
        }

        $parts = Part::where('cutting_plan_id', $cuttingPlanId)
            ->orderBy('part_number')
            ->get(['id', 'part_number', 'cutting_size', 'part_name'])
            ->map(function ($part) {
                $barcode = Barcode::where('part_id', $part->id)->first();

                return [
                    'id' => $part->id,
                    'part_number' => $part->part_number,
                    'cutting_size' => $part->cutting_size,
                    'part_name' => $part->part_name,
                    'barcode_id' => $barcode?->id,
                    'barcode_wbs' => $barcode?->wbs,
                    'barcode_lot' => $barcode?->lot,
                    'barcode_part_no' => $barcode?->part_no,
                ];
            });

        return response()->json(['data' => $parts]);
    }

    public function edit(Barcode $barcode)
    {
        return redirect()->route('barcode.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $perPage = (int) $request->get('per_page', 10);
        $barcodes = $this->baseQuery($request)
            ->orderBy('barcodes.created_at', 'desc')
            ->paginate($perPage);

        return response()->json($barcodes);
    }

    private function baseQuery(Request $request)
    {
        $query = Barcode::query()
            ->leftJoin('cutting_plans', 'barcodes.cutting_plan_id', '=', 'cutting_plans.id')
            ->leftJoin('parts', 'barcodes.part_id', '=', 'parts.id')
            ->leftJoin('users', 'barcodes.user_id', '=', 'users.id')
            ->select(
                'barcodes.*',
                'cutting_plans.no_cp as cp_no',
                'parts.part_number as part_number',
                'users.name as user_name'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('barcodes.wbs', 'LIKE', "%{$search}%")
                    ->orWhere('barcodes.lot', 'LIKE', "%{$search}%")
                    ->orWhere('barcodes.part_no', 'LIKE', "%{$search}%")
                    ->orWhere('cutting_plans.no_cp', 'LIKE', "%{$search}%")
                    ->orWhere('parts.part_number', 'LIKE', "%{$search}%")
                    ->orWhere('users.name', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    private function exportColumns()
    {
        return [
            'cp_no'      => 'Cutting Plan',
            'part_number' => 'Part No',
            'wbs'        => 'WBS',
            'lot'        => 'Lot',
            'part_no'    => 'No Part',
            'tgl'        => 'Tanggal',
            'tgl_print'  => 'Tanggal Print',
        ];
    }

    private function formatExportValue($barcode, $key)
    {
        switch ($key) {
            case 'tgl':
            case 'tgl_print':
                return $barcode->$key
                    ? \Carbon\Carbon::parse($barcode->$key)->translatedFormat('d F Y')
                    : '';
            default:
                return $barcode->$key ?? '';
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
            ->orderBy('barcodes.created_at', 'desc')
            ->get()
            ->map(function ($barcode) use ($columns) {
                $row = [];
                foreach ($columns as $key) {
                    $row[$key] = $this->formatExportValue($barcode, $key);
                }
                return $row;
            });

        $headings = array_map(fn ($c) => $allowed[$c], $columns);

        $export = new CuttingPlanExport($headings, $rows);

        return Excel::download($export, 'barcode.xlsx');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cutting_plan_id' => 'required|exists:cutting_plans,id',
            'part_id'         => 'required|exists:parts,id|unique:barcodes,part_id',
            'wbs'             => 'nullable|string|max:255',
            'lot'             => 'nullable|string|max:255',
            'part_no'         => 'nullable|string|max:255',
            'tgl'             => 'nullable|date',
            'tgl_print'       => 'nullable|date',
        ], [
            'cutting_plan_id.required' => 'Cutting Plan wajib dipilih.',
            'part_id.required'         => 'Part wajib dipilih.',
            'part_id.unique'           => 'Part ini sudah memiliki barcode.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Barcode::create(array_merge($validator->validated(), [
            'user_id' => Auth::id(),
        ]));

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.']);
    }

    public function update(Request $request, Barcode $barcode)
    {
        $validator = Validator::make($request->all(), [
            'wbs'       => 'nullable|string|max:255',
            'lot'       => 'nullable|string|max:255',
            'part_no'   => 'nullable|string|max:255',
            'tgl'       => 'nullable|date',
            'tgl_print' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $barcode->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    public function destroy(Barcode $barcode)
    {
        $barcode->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_values(array_filter((array) $request->input('ids', []), fn ($id) => is_string($id) || is_numeric($id)));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih.'], 422);
        }

        Barcode::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Data terpilih berhasil dihapus.']);
    }
}
