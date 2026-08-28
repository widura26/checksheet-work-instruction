<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\Oka;
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
        $okas = Oka::orderBy('oka')->get(['id', 'oka']);
        $wiTrns = WiTrn::orderBy('no_exp')->get(['id', 'no_exp']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('barcode.index', compact('okas', 'wiTrns', 'users'));
    }

    public function create()
    {
        $okas = Oka::orderBy('oka')->get(['id', 'oka']);
        $wiTrns = WiTrn::orderBy('no_exp')->get(['id', 'no_exp']);

        return view('barcode.create', compact('okas', 'wiTrns'));
    }

    public function edit(Barcode $barcode)
    {
        $okas = Oka::orderBy('oka')->get(['id', 'oka']);
        $wiTrns = WiTrn::orderBy('no_exp')->get(['id', 'no_exp']);

        return view('barcode.edit', compact('barcode', 'okas', 'wiTrns'));
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
            ->leftJoin('okas', 'barcodes.oka_id', '=', 'okas.id')
            ->leftJoin('users', 'barcodes.user_id', '=', 'users.id')
            ->select(
                'barcodes.*',
                'okas.oka as oka_no',
                'users.name as user_name'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_konv', 'LIKE', "%{$search}%")
                    ->orWhere('part_no', 'LIKE', "%{$search}%")
                    ->orWhere('partno1', 'LIKE', "%{$search}%")
                    ->orWhere('wbs', 'LIKE', "%{$search}%")
                    ->orWhere('lot', 'LIKE', "%{$search}%")
                    ->orWhere('PR', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhere('bil', 'LIKE', "%{$search}%")
                    ->orWhere('okas.oka', 'LIKE', "%{$search}%")
                    ->orWhere('users.name', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    private function exportColumns()
    {
        return [
            'no_konv'   => 'No Konv',
            'part_no'   => 'Part No',
            'partno1'   => 'Part No1',
            'wbs'       => 'WBS',
            'lot'       => 'Lot',
            'PR'        => 'PR',
            'oka_no'    => 'Oka',
            'status'    => 'Status',
            'bil'       => 'Bil',
            'tgl'       => 'Tanggal',
            'tgl_print' => 'Tanggal Print',
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
            'no_konv'   => 'required|string|max:255|unique:barcodes,no_konv',
            'oka_id'    => 'nullable|exists:okas,id',
            'wi_trn_id' => 'nullable|exists:work_instructions,id',
            'wbs'       => 'nullable|string|max:255',
            'lot'       => 'nullable|string|max:255',
            'part_no'   => 'nullable|string|max:255',
            'partno1'   => 'nullable|string|max:255',
            'PR'        => 'nullable|string|max:255',
            'status'    => 'nullable|string|max:255',
            'bil'       => 'nullable|string|max:255',
            'tgl'       => 'nullable|date',
            'tgl_print' => 'nullable|date',
        ], [
            'no_konv.required' => 'No Konv wajib diisi.',
            'no_konv.unique'   => 'No Konv sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Barcode::create(array_merge($validator->validated(), [
            'user_id' => Auth::id(),
        ]));

        return redirect()->route('barcode.index')
            ->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, Barcode $barcode)
    {
        $validator = Validator::make($request->all(), [
            'no_konv'   => 'required|string|max:255|unique:barcodes,no_konv,' . $barcode->id . ',id',
            'oka_id'    => 'nullable|exists:okas,id',
            'wi_trn_id' => 'nullable|exists:work_instructions,id',
            'wbs'       => 'nullable|string|max:255',
            'lot'       => 'nullable|string|max:255',
            'part_no'   => 'nullable|string|max:255',
            'partno1'   => 'nullable|string|max:255',
            'PR'        => 'nullable|string|max:255',
            'status'    => 'nullable|string|max:255',
            'bil'       => 'nullable|string|max:255',
            'tgl'       => 'nullable|date',
            'tgl_print' => 'nullable|date',
        ], [
            'no_konv.required' => 'No Konv wajib diisi.',
            'no_konv.unique'   => 'No Konv sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $barcode->update($validator->validated());

        return redirect()->route('barcode.index')
            ->with('success', 'Data berhasil diperbarui.');
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
