<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Spm;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SpmController extends Controller
{
    public function index()
    {
        return view('pages.spm.index');
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $query = Spm::with(['project' => function ($q) {
                $q->withoutGlobalScope('onlyWithStock');
            }, 'posisiCt', 'createdBy']);

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
            $query->orderBy('created_at', 'desc');
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('project_id', fn($row) => $row->project->nama_project)
                ->addColumn('tanggal_spm', fn($row) => \Carbon\Carbon::parse($row->tanggal_spm)->format('d-m-Y'))
                ->addColumn('status', function ($row) {
                    return match ($row->status) {
                        'COMPLETED' => '<span class="badge bg-success">SELESAI</span>',
                        'WAITING' => '<span class="badge bg-secondary">MENUNGGU</span>',
                        'CANCELED' => '<span class="badge bg-danger">DI BATALKAN</span>',
                        default => '<span class="badge bg-light text-dark">Unknown</span>',
                    };
                })
                ->addColumn('action', function ($row) {
                    $ajukanUlangUrl = route('spm.ajukan-ulang', $row->id);
                    if (! in_array($row->status, ['CANCELED'])) {
                        $showUrl = route('spm.show', $row->id);
                        $printUrl = route('spm.print', $row->id);
                        $cancelUrl = route('spm.cancel', $row->id);
                        $eyeIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-fullscreen"></use></svg>';
                        $printIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-print"></use></svg>';
                        $cancelIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-x-circle"></use></svg>';
                        $buttons = '<div class="btn-group d-flex justify-content-center align-items-center gap-2">
                                <a href="' . $showUrl . '" class="btn btn-info text-white rounded">' . $eyeIcon . '</a>';
                        $buttons .= '<a href="' . $printUrl . '" target="_blank" class="btn btn-warning text-white rounded">' . $printIcon . '</a>';
                        if (Auth::check() && Auth::user()->hasRole('admin') && in_array($row->status, ['WAITING']) && $row->file_ttd === null) {
                            $buttons .= '
        <form action="' . $cancelUrl . '" method="POST" onsubmit="return confirm(\'Yakin ingin membatalkan?\')" style="display:inline;">
            ' . csrf_field() . '
            ' . method_field('POST') . '
            <button type="submit" class="btn btn-danger text-white">
                ' . $cancelIcon . '
            </button>
        </form>';
                        }
                        $buttons .= '</div>';

                        return $buttons;
                    }

                    return ' <a href="' . $ajukanUlangUrl . '" class="btn btn-info text-white rounded">Ajukan Ulang</a>';
                })
                ->addColumn('kedatangan', function ($row) {
                    $terimaUrl = route('spm.terima', $row->id);
                    $buttons = '';
                    if (! in_array($row->status, ['COMPLETED', 'PARTIAL', 'CANCELED'])) {
                        $buttons = '<a href="' . $terimaUrl . '" class="btn btn-success btn-sm text-white ms-2">' . 'Terima Barang' . '</a>';
                    } else {
                        if ($row->file_ttd) {
                            $downloadUrl = route('spm.download', $row->id);
                            $downloadIcon = '<svg class="icon"><use xlink:href="' . asset('template/vendors/@coreui/icons/svg/free.svg') . '#cil-cloud-download"></use></svg>';
                            $buttons = '<a href="' . $downloadUrl . '" class="btn btn-success btn-sm text-white" target="_blank">' . $downloadIcon . '</a>';
                        }
                    }

                    return $buttons;
                })
                ->rawColumns(['action', 'status', 'kedatangan'])
                ->make(true);
        }
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
            $projects = Project::orderBy('nama_project')->get(['project_id', 'nama_project as name']);
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
