<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\DaftarDistribusiTool;
use App\Models\Departemen;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BagianController extends Controller
{
    public function index()
    {
        return view('pages.lokasi.bagian.index');
    }

    public function getData(Request $request)
    {
        if (!$request->ajax()) {
            abort(403);
        }
        $bagian = Bagian::with('departemen')
            ->withCount('distribusiTools');

        return DataTables::of($bagian)
            ->addIndexColumn()
            ->addColumn('action', function ($bagian) {
                $buttons = '<div class="btn-group d-flex justify-content-center gap-2" role="group">';
                if (Auth::user()->hasRole(['admin'])) {
                    $buttons .= '
                    <form action="' . route('lokasi.bagian.toggle', $bagian->id) . '" method="POST">
                                        ' . csrf_field() . '
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                onchange="this.form.submit()" id="flexSwitchCheckChecked" ' . ($bagian->is_active == true ? 'checked' : '') . '>
                                        </div>
                                    </form>
                ';
                }
                $buttons .= '<a href="' . route('lokasi.bagian.edit', $bagian->id) . '" class="btn btn-primary btn-sm">Edit</a>';
                $buttons .= '<a href="' . route('lokasi.bagian.distribusi', $bagian->id) . '" class="btn btn-info btn-sm text-white" title="Lihat Distribusi">
                   Lihat Distribusi
                </a>';
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['action', 'jumlah_distribusi'])
            ->make(true);
    }

    public function create()
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $departemens = Departemen::all();

        return view('pages.lokasi.bagian.create', compact('departemens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bagian' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemens,id',
        ]);

        Bagian::create($request->all());

        return redirect()->route('lokasi.bagian.index')->with('success', 'Bagian created successfully.');
    }

    public function edit($id)
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $bagian = Bagian::findOrFail($id);
        $departemens = Departemen::all();

        return view('pages.lokasi.bagian.edit', compact('bagian', 'departemens'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bagian' => 'required|string|max:100',
            'departemen_id' => 'required|exists:departemens,id',
        ]);

        $bagian = Bagian::findOrFail($id);
        $bagian->update($request->all());

        return redirect()->route('lokasi.bagian.index')->with('success', 'Bagian updated successfully.');
    }

    public function destroy($id)
    {
        Bagian::findOrFail($id)->delete();

        return redirect()->route('lokasi.bagian.index')->with('success', 'Bagian deleted successfully.');
    }

    public function distribusi($id)
    {
        $bagian = Bagian::with('departemen')->findOrFail($id);

        return view('pages.lokasi.bagian.distribusi', compact('bagian'));
    }

    public function getDistribusiData($id)
    {
        $distribusi = \App\Models\DistribusiTool::where('bagian_id', $id)
            ->where('status', '<>', 'batal')
            ->with(['daftarDistribusiTool.tool', 'project', 'projectDest', 'departemen', 'unit']);

        return DataTables::of($distribusi)
            ->addIndexColumn()
            ->addColumn('tanggal', function ($row) {
                return $row->tanggal_pinjam_diserahkan->format('d/m/Y H:i');
            })
            ->addColumn('type_badge', function ($row) {
                $color = $row->type == 'peminjaman' ? 'primary' : 'success';
                return '<span class="badge bg-' . $color . '">' . ucfirst($row->type) . '</span>';
            })
            ->addColumn('kategori_badge', function ($row) {
                $kategori = strtolower($row->kategori);

                $badgeClass = match ($kategori) {
                    'tool' => 'bg-warning',
                    'consumable' => 'bg-primary',
                    'apd' => 'bg-info',
                    default => 'bg-secondary',
                };
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->kategori) . '</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $statusColors = [
                    'kembali' => 'success',
                    'kembali_sebagian' => 'warning',
                    'diserahkan' => 'primary',
                    'belum_kembali' => 'danger',
                    'batal' => 'dark'
                ];
                $color = $statusColors[$row->status] ?? 'secondary';
                return '<span class="badge bg-' . $color . '">' . ucwords(str_replace('_', ' ', $row->status)) . '</span>';
            })
            ->addColumn('jumlah_material', function ($row) {
                $totalItem = $row->daftarDistribusiTool->sum('jumlah_distribusi');
                $jumlahJenis = $row->daftarDistribusiTool->count();
                return $totalItem . ' unit (' . $jumlahJenis . ' jenis)';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('distribusi.show', $row->public_id) . '" class="btn btn-sm btn-info text-white">Detail</a>';
            })
            ->rawColumns(['type_badge', 'kategori_badge', 'status_badge', 'action'])
            ->make(true);
    }
    public function toggleBagian(Bagian $bagian)
    {
        $bagianActive = $bagian->is_active;
        $bagian->is_active = !$bagian->is_active;
        $bagian->save();
        Unit::where('bagian_id', $bagian->id)->update(['is_active' => !$bagianActive]);
        return redirect()->route('lokasi.bagian.index')->with('success', 'bagian Berhasil Di Non Aktifkan.');
    }
}
