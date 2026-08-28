<?php

namespace App\Http\Controllers;

use App\Models\Bpm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                $q->where('bpms.no_bpm', 'LIKE', "%{$search}%")
                    ->orWhere('materials.deskripsi', 'LIKE', "%{$search}%")
                    ->orWhere('materials.spesifikasi', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.jumlah', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.satuan', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.no_bprm', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.ket_bpm', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.stok', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    private function exportColumns()
    {
        return [
            'no_bpm'         => 'No BPM',
            'material_name'  => 'Material',
            'material_spec'  => 'Spesification',
            'jumlah'         => 'Jumlah',
            'satuan'         => 'Unit',
            'tgl_permintaan' => 'Tanggal Permintaan',
            'ket_bpm'        => 'Keterangan',
            'no_bprm'        => 'Nomor BPRM',
            'tgl_bprm'       => 'Tanggal BPRM',
            'jml_bprm'       => 'Jumlah BPRM',
            'stok'           => 'Stok',
        ];
    }

    private function formatExportValue($row, $key)
    {
        switch ($key) {
            case 'tgl_permintaan':
            case 'tgl_bprm':
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
