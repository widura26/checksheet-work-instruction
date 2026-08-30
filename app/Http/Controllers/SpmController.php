<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Spm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SpmController extends Controller
{
    public function index()
    {
        return view('pages.spm.index');
    }

    public function create()
    {
        return view('pages.spm.create');
    }

    public function ajukanUlang(Spm $spm)
    {
        return view('pages.spm.ajukan-ulang', compact('spm'));
    }

    public function destroy(Spm $spm)
    {
        $spm->items()->delete();
        $spm->delete();

        return response()->json(['success' => true, 'message' => 'SPM berhasil dihapus.']);
    }

    public function getData(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $perPage = (int) $request->get('per_page', 10);
        $query = Spm::with(['project' => function ($q) {
            $q->withoutGlobalScope('onlyWithStock');
        }, 'createdBy']);

        if ($request->filled('no_spm')) {
            $query->where('no_spm', 'like', '%' . $request->no_spm . '%');
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('tanggal_spm')) {
            $query->whereDate('tanggal_spm', $request->tanggal_spm);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_spm', 'LIKE', "%{$search}%")
                    ->orWhere('kepada', 'LIKE', "%{$search}%");
            });
        }

        $rows = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->through(fn ($spm) => [
                'id' => $spm->id,
                'no_spm' => $spm->no_spm,
                'project_name' => $spm->project?->name_project ?? '-',
                'kepada' => $spm->kepada,
                'tanggal_spm' => Carbon::parse($spm->tanggal_spm)->format('d-m-Y'),
                'status' => $spm->status,
                'file' => $spm->file,
                'file_ttd' => $spm->file_ttd,
                'created_by' => $spm->createdBy?->name ?? '-',
            ]);

        return response()->json($rows);
    }

    public function show(Spm $spm)
    {
        $spm->load(['items.itemHistory']);

        return view('pages.spm.show', compact('spm'));
    }

    public function print(Spm $spm)
    {
        if (!$spm->file || !Storage::disk('public')->exists($spm->file)) {
            return redirect()->route('spm.index')->with('error', 'File SPM tidak ditemukan.');
        }
        $filePath = storage_path('app/public/' . $spm->file);
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($spm->file) . '"'
        ]);
    }

    public function terima(Spm $spm)
    {
        if ($spm->status === 'COMPLETED' || $spm->status == 'PARTIAL') {
            abort(403);
        }

        return view('pages.spm.terima', compact('spm'));
    }

    public function cancelSpm(Spm $spm)
    {
        $spm->update(['status' => 'CANCELED']);
        if ($spm->file && Storage::disk('public')->exists($spm->file)) {
            Storage::disk('public')->delete($spm->file);
        }
        return redirect()->route('spm.index')->with('success', 'SPM berhasil dibatalkan.');
    }

    public function download(Spm $spm)
    {
        if ($spm->file_ttd && Storage::disk('public')->exists($spm->file_ttd)) {
            return Storage::disk('public')->response($spm->file_ttd);
        }

        return redirect()->route('spm.index')->with('error', 'File tidak ditemukan.');
    }

    public function getProjects(Request $request)
    {
        if ($request->ajax()) {
            $projects = Project::orderBy('name_project')->get(['project_id', 'name_project as name']);
            return response()->json($projects);
        }
        abort(403);
    }

    public function getStatuses(Request $request)
    {
        if ($request->ajax()) {
            $statuses = Spm::select('status')->distinct()->get()->map(function ($status) {
                return [
                    'value' => $status->status,
                    'name' => ucfirst(str_replace('-', ' ', $status->status))
                ];
            });
            return response()->json($statuses);
        }
        abort(403);
    }
}
