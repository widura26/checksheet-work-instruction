<?php

namespace App\Http\Controllers;

use App\Models\CuttingPlan;
use App\Models\CuttingPlanMaterial;
use App\Models\Material;
use App\Models\Part;
use App\Models\Project;
use App\Models\Proses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CuttingPlanController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        $processes = Proses::all();
        return view('cutting-plan.index', compact('projects', 'processes'));
    }

    public function create()
    {
        $projects = Project::all();
        $processes = Proses::all();
        return view('cutting-plan.create', compact('projects', 'processes'));
    }

    private function normalizeEmpty(Request $request)
    {
        $data = $request->all();
        array_walk_recursive($data, function (&$value) {
            if ($value === '') {
                $value = null;
            }
        });
        $request->merge($data);
    }

    public function getData(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $perPage = (int) $request->get('per_page', 10);
        $cps = $this->baseQuery($request)
            ->orderBy('cutting_plans.created_at', 'desc')
            ->paginate($perPage);

        return response()->json($cps);
    }

    private function baseQuery(Request $request)
    {
        $query = CuttingPlan::query()
            ->with(['materials.parts', 'materials.material', 'process'])
            ->leftJoin('projects', 'cutting_plans.project_id', '=', 'projects.project_id')
            ->leftJoin('processes', 'cutting_plans.process_id', '=', 'processes.id')
            ->leftJoin('bpms', 'bpms.cutting_plan_id', '=', 'cutting_plans.id')
            ->leftJoin('users as u_prepared', 'cutting_plans.prepared_by', '=', 'u_prepared.id')
            ->leftJoin('users as u_approved', 'cutting_plans.approved_by', '=', 'u_approved.id')
            ->leftJoin('users as u_operator', 'cutting_plans.date_operator', '=', 'u_operator.id')
            ->select(
                'cutting_plans.*',
                'projects.project_id as kode_proyek',
                'projects.name_project as project_name',
                'processes.nama as cutting_method',
                'bpms.bpm_no as bpm_number',
                'u_prepared.name as prepared_by_name',
                'u_approved.name as approved_by_name',
                'u_operator.name as date_operator_name',
                DB::raw('NULL as date_of_bpm')
            )
            ->addSelect('cutting_plans.process_id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('cutting_plans.no_cp', 'LIKE', "%{$search}%")
                    ->orWhere('processes.nama', 'LIKE', "%{$search}%")
                    ->orWhere('bpms.no_bpm', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    private function exportColumns()
    {
        return [
            'no_cp'               => 'No CP',
            'kode_proyek'         => 'Kode Proyek',
            'cutting_method'      => 'Cutting Method',
            'sketch'              => 'Sket',
            'materials_data'      => 'Materials',
            'special_instruction' => 'Special Instruction',
            'date_issued'         => 'Date Issued',
            'date_revised'        => 'Date Revised',
            'bpm_number'          => 'BPM Number',
            'date_of_bpm'         => 'Date of BPM',
        ];
    }

    private function formatExportValue($cp, $key)
    {
        switch ($key) {
            case 'date_issued':
            case 'date_revised':
            case 'date_of_bpm':
                return $cp->$key
                    ? \Carbon\Carbon::parse($cp->$key)->translatedFormat('d F Y')
                    : '';
            case 'materials_data':
                $count = $cp->materials->sum(fn ($m) => $m->parts->count());
                return $count . ' part(s) across ' . $cp->materials->count() . ' material(s)';
            default:
                return $cp->$key ?? '';
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
            ->orderBy('cutting_plans.created_at', 'desc')
            ->get()
            ->map(function ($cp) use ($columns) {
                $row = [];
                foreach ($columns as $key) {
                    $row[$key] = $this->formatExportValue($cp, $key);
                }
                return $row;
            });

        $headings = array_map(fn ($c) => $allowed[$c], $columns);

        $export = new \App\Exports\CuttingPlanExport($headings, $rows);

        return Excel::download($export, 'cutting-plan.xlsx');
    }

    public function store(Request $request)
    {
        $this->normalizeEmpty($request);

        $validator = Validator::make($request->all(), [
            'no_cp' => 'required|string|max:255|unique:cutting_plans,no_cp',
            'project_id' => 'nullable|string|max:255',
            'sketch' => 'nullable|string|max:255',
            'process_id' => 'nullable|string|max:255',
            'special_instruction' => 'nullable|string',
            'date_issued' => 'nullable|date',
            'date_revised' => 'nullable|date',
            'prepared_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'date_operator' => 'nullable|string|max:255',
            'bpm_number' => 'nullable|string|max:255',
            'date_of_bpm' => 'nullable|date',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'nullable|string|max:255',
            'materials.*.material_size' => 'nullable|string|max:255',
            'materials.*.quantity' => 'nullable|integer',
            'materials.*.wbs' => 'nullable|string|max:255',
            'materials.*.parts' => 'nullable|array',
            'materials.*.parts.*.part_no' => 'nullable|string|max:255',
            'materials.*.parts.*.cutting_size' => 'nullable|string|max:255',
            'materials.*.parts.*.qty_car' => 'nullable|integer',
            'materials.*.parts.*.qty_lot' => 'nullable|integer',
            'materials.*.parts.*.part_name' => 'nullable|string|max:255',
            'materials.*.parts.*.remark' => 'nullable|string|max:255',
        ], [
            'no_cp.required' => 'No CP wajib diisi.',
            'no_cp.unique' => 'No CP sudah digunakan.',
            'materials.required' => 'Minimal harus ada 1 material.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cuttingPlan = CuttingPlan::create($validator->validated());

        $this->syncMaterials($cuttingPlan, $request->input('materials', []));

        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan.']);
    }

    public function update(Request $request, CuttingPlan $cuttingPlan)
    {
        $this->normalizeEmpty($request);

        $validator = Validator::make($request->all(), [
            'no_cp' => 'required|string|max:255|unique:cutting_plans,no_cp,' . $cuttingPlan->id . ',id',
            'project_id' => 'nullable|string|max:255',
            'sketch' => 'nullable|string|max:255',
            'process_id' => 'nullable|string|max:255',
            'special_instruction' => 'nullable|string',
            'date_issued' => 'nullable|date',
            'date_revised' => 'nullable|date',
            'prepared_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'date_operator' => 'nullable|string|max:255',
            'bpm_number' => 'nullable|string|max:255',
            'date_of_bpm' => 'nullable|date',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'nullable|string|max:255',
            'materials.*.material_size' => 'nullable|string|max:255',
            'materials.*.quantity' => 'nullable|integer',
            'materials.*.wbs' => 'nullable|string|max:255',
            'materials.*.parts' => 'nullable|array',
            'materials.*.parts.*.part_no' => 'nullable|string|max:255',
            'materials.*.parts.*.cutting_size' => 'nullable|string|max:255',
            'materials.*.parts.*.qty_car' => 'nullable|integer',
            'materials.*.parts.*.qty_lot' => 'nullable|integer',
            'materials.*.parts.*.part_name' => 'nullable|string|max:255',
            'materials.*.parts.*.remark' => 'nullable|string|max:255',
        ], [
            'no_cp.required' => 'No CP wajib diisi.',
            'no_cp.unique' => 'No CP sudah digunakan.',
            'materials.required' => 'Minimal harus ada 1 material.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cuttingPlan->update($validator->validated());

        $this->syncMaterials($cuttingPlan, $request->input('materials', []));

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui.']);
    }

    private function syncMaterials(CuttingPlan $cuttingPlan, array $materialsData)
    {
        $cuttingPlan->materials()->each(function ($cpm) {
            $cpm->parts()->delete();
        });
        $cuttingPlan->materials()->delete();

        $projectId = Project::where('project_id', request()->input('project_id'))->value('id')
            ?? request()->input('project_id');

        foreach ($materialsData as $mData) {
            if (! is_array($mData)) {
                continue;
            }

            $cpm = CuttingPlanMaterial::create([
                'cutting_plan_id' => $cuttingPlan->id,
                'material_id' => $mData['material_id'] ?? null,
                'material_size' => $mData['material_size'] ?? null,
                'quantity' => isset($mData['quantity']) && $mData['quantity'] !== '' ? (int) $mData['quantity'] : null,
                'wbs' => $mData['wbs'] ?? null,
            ]);

            $parts = $mData['parts'] ?? [];
            foreach ($parts as $pp) {
                if (! is_array($pp)) {
                    continue;
                }

                $filled = collect($pp)->filter(fn ($v) => $v !== null && $v !== '')->count();
                if ($filled === 0) {
                    continue;
                }

                Part::create([
                    'cutting_plan_material_id' => $cpm->id,
                    'part_number' => $pp['part_no'] ?? null,
                    'cutting_size' => $pp['cutting_size'] ?? null,
                    'quantity_per_car' => isset($pp['qty_car']) && $pp['qty_car'] !== '' ? (int) $pp['qty_car'] : null,
                    'quantity_per_lot' => isset($pp['qty_lot']) && $pp['qty_lot'] !== '' ? (int) $pp['qty_lot'] : null,
                    'part_name' => $pp['part_name'] ?? null,
                    'remark' => $pp['remark'] ?? null,
                ]);
            }
        }
    }

    public function destroy(CuttingPlan $cuttingPlan)
    {
        $cuttingPlan->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function bulkDestroy(Request $request)
    {
        $ids = array_values(array_filter((array) $request->input('ids', []), fn ($id) => is_numeric($id)));

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data yang dipilih.'], 422);
        }

        CuttingPlan::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Data terpilih berhasil dihapus.']);
    }

    public function users(Request $request)
    {
        $search = $request->get('q', '');

        $query = \App\Models\User::query();

        if ($search !== '') {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $users = $query->select('id', 'name')->orderBy('name')->limit(50)->get();

        $results = $users->map(function ($u) {
            return [
                'id' => $u->id,
                'text' => $u->name,
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function projects(Request $request)
    {
        $search = $request->get('q', '');

        $query = Project::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('project_id', 'LIKE', "%{$search}%")
                    ->orWhere('name_project', 'LIKE', "%{$search}%");
            });
        }

        $items = $query->orderBy('project_id')->limit(20)->get(['project_id', 'name_project']);

        $results = $items->map(function ($p) {
            return [
                'id' => $p->project_id,
                'text' => $p->project_id . ' - ' . $p->name_project,
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function processes(Request $request)
    {
        $search = $request->get('q', '');

        $query = Proses::query();

        if ($search !== '') {
            $query->where('nama', 'LIKE', "%{$search}%");
        }

        $items = $query->orderBy('nama')->limit(20)->get(['id', 'nama']);

        $results = $items->map(function ($p) {
            return [
                'id' => $p->id,
                'text' => $p->nama,
            ];
        });

        return response()->json(['results' => $results]);
    }

    public function materials(Request $request)
    {
        $search = $request->get('q', '');

        $query = Material::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_material', 'LIKE', "%{$search}%")
                    ->orWhere('deskripsi', 'LIKE', "%{$search}%")
                    ->orWhere('spesifikasi', 'LIKE', "%{$search}%");
            });
        }

        $materials = $query->orderBy('kode_material')
            ->limit(20)
            ->get(['id', 'kode_material', 'deskripsi', 'spesifikasi']);

        $results = $materials->map(function ($m) {
            return [
                'id' => $m->id,
                'text' => $m->kode_material . ' - ' . $m->deskripsi,
            ];
        });

        return response()->json(['results' => $results]);
    }
}
