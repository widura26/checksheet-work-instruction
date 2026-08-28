<?php

namespace App\Http\Controllers;

use App\Models\Bpm;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BpmController extends Controller
{
    public function index()
    {
        return view('bon-permintaan-material.index');
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
            ->leftJoin('materials', 'bpms.material_id', '=', 'materials.id')
            ->select(
                'bpms.*',
                'materials.deskripsi as material_name',
                'materials.spesifikasi as material_spec'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bpms.bpm_no', 'LIKE', "%{$search}%")
                    ->orWhere('materials.deskripsi', 'LIKE', "%{$search}%")
                    ->orWhere('materials.spesifikasi', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.total', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.unit', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.bprm_no', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.specification', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.stock', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    private function exportColumns()
    {
        return [
            'bpm_no'        => 'No BPM',
            'material_name' => 'Material',
            'material_spec' => 'Spesification',
            'total'         => 'Jumlah',
            'unit'          => 'Unit',
            'request_date'  => 'Tanggal Permintaan',
            'specification' => 'Keterangan',
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

        Bpm::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Data terpilih berhasil dihapus.']);
    }
}
