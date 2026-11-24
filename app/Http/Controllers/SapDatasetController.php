<?php

namespace App\Http\Controllers;

use App\Models\SapDataset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Exports\SapDatasetExport;
use App\Jobs\ImportExcelJob;
use App\Services\DataProcessService;
use App\Models\DaftarKebutuhanMaterial;
use App\Support\ActivityLogger;
use App\Support\ImportTracker;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class SapDatasetController extends Controller
{
    public function index(DataProcessService $svc){
        $options = Cache::remember('mp:options:v' . Cache::get('mp:version', 1), 300,
            fn () => $svc->filterOptions());
        $defaultTrainset = 'Semua';

        return view('material-planning.index', compact('options','defaultTrainset'));
    }

    public function data(Request $request, DataProcessService $svc){
        $proyek   = (string) $request->input('proyek', 'Semua');
        $group    = (string) $request->input('group', 'Semua');
        $trainset = (string) $request->input('trainset', 'Semua');
        $statuses = $this->statusesFromRequest($request);

        return response()->json($svc->paginateDataset(
            $proyek,
            $group,
            $trainset,
            $statuses,
            (string) $request->input('search', ''),
            (string) $request->input('sort', 'no'),
            (string) $request->input('dir', 'asc'),
            (int) $request->input('page', 1),
            (int) $request->input('per_page', 10)
        ));
    }

    public function export(Request $request, DataProcessService $svc){
        $proyek   = (string) $request->input('proyek', 'Semua');
        $group    = (string) $request->input('group', 'Semua');
        $trainset = (string) $request->input('trainset', 'Semua');
        $statuses = $this->statusesFromRequest($request);
        $search   = trim((string) $request->input('search', ''));
        $format   = strtolower((string) $request->input('format', 'csv'));

        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('daftar_kebutuhan_materials');

        $query = DaftarKebutuhanMaterial::query()->select($columns);

        $dbGroup = DaftarKebutuhanMaterial::query()->getGrammar()->wrap('group');
        $query->whereNotNull('group')->whereRaw("TRIM($dbGroup) <> ''");

        if ($proyek !== 'Semua') {
            $query->where('wbs', $proyek);
        }
        if ($group !== 'Semua') {
            $query->where('group', $group);
        }
        if ($trainset !== 'Semua') {
            $query->where('lot', $trainset);
        }

        $query->orderBy('no')->orderBy('kode_material');

        $baseName = 'Dataset_BOM_Monitoring_' . now()->format('Ymd_His');

        ActivityLogger::record('export', 'Export dataset BOM Monitoring', [
            'format' => ($format === 'xlsx' || $format === 'excel') ? 'xlsx' : 'csv',
            'proyek' => $proyek,
            'group' => $group,
            'trainset' => $trainset,
            'statuses' => $statuses,
            'search' => $search,
        ], null, 'SapDataset');

        if ($format === 'xlsx' || $format === 'excel') {
            $rows = collect();
            foreach ($query->cursor() as $row) {
                if ($svc->matchesExportFilters($row, $statuses, $search)) {
                    $rows->push($row);
                }
            }

            return Excel::download(
                new SapDatasetExport($rows, $columns),
                $baseName . '.xlsx'
            );
        }

        $filename = $baseName . '.csv';

        return response()->streamDownload(function () use ($query, $columns, $svc, $statuses, $search) {
            $output = fopen('php://output', 'w');

            // BOM + delimiter semicolon agar terbaca rapi per kolom di Excel.
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, $columns, ';');

            foreach ($query->cursor() as $row) {
                if (! $svc->matchesExportFilters($row, $statuses, $search)) {
                    continue;
                }

                $data = [];
                foreach ($columns as $column) {
                    $data[] = $row->{$column};
                }
                fputcsv($output, $data, ';');
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function statusesFromRequest(Request $request): array {
        $statuses = $request->input('statuses', []);

        if (is_string($statuses)) {
            $statuses = explode(',', $statuses);
        }

        if (!is_array($statuses)) {
            return [];
        }

        return array_values(array_intersect($statuses, DataProcessService::STATUS_PR_ORDER));
    }

    public function importStatus(string $importId){
        if (!Str::isUuid($importId)) {
            return response()->json([
                'import_id' => $importId,
                'status' => 'invalid',
                'message' => 'Format import ID tidak valid.',
            ], 422);
        }

        $state = ImportTracker::get($importId);

        if ($state === null) {
            return response()->json([
                'import_id' => $importId,
                'status' => 'not_found',
                'message' => 'Import ID tidak ditemukan atau sudah kedaluwarsa.',
            ], 404);
        }

        return response()->json($state);
    }

    public function delete(Request $request){
        if (!auth()->user()->hasRole(['admin'])) {
            abort(403);
        }

        $request->validate([
            'confirm_delete' => 'required|string|in:HAPUS',
        ], [
            'confirm_delete.required' => 'Konfirmasi penghapusan wajib diisi.',
            'confirm_delete.in' => 'Konfirmasi tidak valid.',
        ]);

        try {
            $deletedCount = SapDataset::query()->count();

            DB::transaction(function () {
                SapDataset::query()->delete();
            });
            Cache::forget('mp:version');
            Log::warning('Seluruh data SAP dataset dihapus oleh user.', [
                'user_id' => auth()->id(),
            ]);

            ActivityLogger::record('delete_all', 'Hapus seluruh data SAP dataset', [
                'deleted_count' => $deletedCount,
            ], null, 'SapDataset');

            return redirect()->back()->with('success', 'Data SAP dataset berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data SAP dataset: ' . $e->getMessage(), ['user_id' => auth()->id()]);
            return redirect()->back()->with('error', 'Gagal menghapus data SAP dataset.');
        }
    }

    public function deviation(Request $request){
        if (! $request->ajax()) {
            abort(404);
        }

        $query = SapDataset::select(
            'pr_number',
            DB::raw('MIN(wbs_elem) as wbs_elem'),
            DB::raw('MIN(wbs_description) as wbs_description'),
            DB::raw('MIN(material_code) as material_code'),
            DB::raw('MIN(material_description) as material_description'),
            DB::raw('MIN(unit) as unit'),
            DB::raw('MIN(po_number) as po_number'),
            DB::raw('MIN(created_by) as created_by'),
            DB::raw('COUNT(*) as total_records'),
            DB::raw('SUM(CAST(REPLACE(requested_quantity, ",", ".") AS DECIMAL(15,2))) as total_requested'),
            DB::raw('SUM(CAST(REPLACE(ordered_quantity, ",", ".") AS DECIMAL(15,2))) as total_ordered'),
            DB::raw('SUM(CAST(REPLACE(requested_quantity, ",", ".") AS DECIMAL(15,2))) - SUM(CAST(REPLACE(ordered_quantity, ",", ".") AS DECIMAL(15,2))) as selisih')
        )
        ->groupBy('pr_number')
        ->havingRaw('SUM(CAST(REPLACE(requested_quantity, ",", ".") AS DECIMAL(15,2))) != SUM(CAST(REPLACE(ordered_quantity, ",", ".") AS DECIMAL(15,2)))');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('pr_number', 'LIKE', "%{$search}%")
                    ->orWhere('wbs_description', 'LIKE', "%{$search}%")
                    ->orWhere('material_description', 'LIKE', "%{$search}%")
                    ->orWhereIn('pr_number', function ($sub) use ($search) {
                        $sub->select('pr_number')
                            ->from('sap_datasets')
                            ->where('material_code', 'LIKE', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $data = $query->orderBy('pr_number')->paginate($perPage);

        $data->setCollection(
            $data->getCollection()->map(function ($item) {
                return [
                    'pr_number'            => $item->pr_number,
                    'wbs_elem'             => $item->wbs_elem,
                    'wbs_description'      => $item->wbs_description,
                    'material_code'        => $item->material_code,
                    'material_description' => $item->material_description,
                    'unit'                 => $item->unit,
                    'po_number'            => $item->po_number,
                    'created_by'           => $item->created_by,
                    'total_records'        => $item->total_records,
                    'total_requested'      => number_format($item->total_requested, 2, ',', '.'),
                    'total_ordered'        => number_format($item->total_ordered, 2, ',', '.'),
                    'selisih'              => number_format($item->selisih, 2, ',', '.'),
                ];
            })
        );

        return response()->json($data);
    }

    public function import_excel(Request $request){
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:20480',
            ], [
                'file.required' => 'File wajib dipilih.',
                'file.mimes' => 'File harus berformat xlsx atau xls.',
                'file.max' => 'Ukuran file maksimal 20 MB.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        }

        $importId = (string) Str::uuid();

        try {
            $file = $request->file('file');
            $path = $file->storeAs('imports', $importId . '.' . $file->getClientOriginalExtension(), 'local');

            ImportTracker::start($importId);

            set_time_limit(0);
            ini_set('memory_limit', '512M');

            ImportExcelJob::dispatchSync($importId, $path);

            ActivityLogger::record('import', 'Import daftar kebutuhan material', [
                'file' => $file->getClientOriginalName(),
                'import_id' => $importId,
            ], null, 'SapDataset');

            return response()->json([
                'success' => true,
                'import_id' => $importId,
                'message' => 'Import selesai diproses. Gunakan import_id untuk cek status.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal memulai import daftar kebutuhan material: ' . $e->getMessage(), [
                'import_id' => $importId,
                'exception' => $e,
            ]);

            ImportTracker::failed($importId, $e->getMessage());

            return response()->json([
                'success' => false,
                'import_id' => $importId,
                'message' => 'Gagal memproses import.',
            ], 500);
        }
    }

    public function resetData(){
        try {
            $deletedCount = DaftarKebutuhanMaterial::query()->delete();

            ImportTracker::invalidateCache();

            Log::warning('Seluruh data daftar kebutuhan material dihapus oleh user.', [
                'user_id' => auth()->id(),
                'deleted_count' => $deletedCount,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus data daftar kebutuhan material: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Data gagal dihapus'
            ], 500);
        }
    }
}
