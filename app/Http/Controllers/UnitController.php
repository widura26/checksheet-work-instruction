<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\DaftarDistribusiTool;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function index()
    {
        return view('pages.lokasi.unit.index');
    }

    public function data()
    {
        $unit = Unit::with('bagian');

        return DataTables::of($unit)
            ->addIndexColumn()
            ->addColumn('action', function ($unit) {
                $buttons = '<div class="btn-group d-flex justify-content-center gap-2" role="group">';
                if (Auth::user()->hasRole(['admin'])) {
                    $buttons .= '
                    <form action="' . route('lokasi.unit.toggle', $unit->id) . '" method="POST">
                                        ' . csrf_field() . '
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" role="switch"
                                                onchange="this.form.submit()" id="flexSwitchCheckChecked" ' . ($unit->is_active == true ? 'checked' : '') . '>
                                        </div>
                                    </form>
                ';
                }
                $buttons .= '<a href="' . route('lokasi.unit.edit', $unit->id) . '" class="btn btn-primary btn-sm">Edit</a>';
                $buttons .= '<a href="' . route('lokasi.unit.distribusi', $unit->id) . '" class="btn btn-info btn-sm text-white" title="Lihat Distribusi">
                   Lihat Distribusi
                </a>';
                $buttons .= '</div>';
                return $buttons;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $units = unit::all();
        $bagians = Bagian::all();
        return view('pages.lokasi.unit.create', compact('units', 'bagians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'bagian_id' => 'required|exists:bagians,id',
        ]);

        Unit::create($request->all());

        return redirect()->route('lokasi.unit.index')->with('success', 'Unit created successfully.');
    }

    public function edit($id)
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $unit = Unit::findOrFail($id);
        $units = unit::all();
        $bagians = Bagian::all();
        return view('pages.lokasi.unit.edit', compact('unit', 'units', 'bagians'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'bagian_id' => 'required|exists:bagians,id',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->update($request->all());

        return redirect()->route('lokasi.unit.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy($id)
    {
        Unit::findOrFail($id)->delete();

        return redirect()->route('lokasi.unit.index')->with('success', 'Unit deleted successfully.');
    }

    public function distribusi($id)
    {
        $unit = Unit::with('bagian.departemen')->findOrFail($id);
        return view('pages.lokasi.unit.distribusi', compact('unit'));
    }

    public function getDistribusiData($id)
    {
        $distribusi = \App\Models\DistribusiTool::where('unit_id', $id)
            ->where('status', '<>', 'batal')
            ->with(['daftarDistribusiTool.tool', 'project', 'projectDest', 'departemen', 'unit', 'bagian']);

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
                    'kembali_seunit' => 'warning',
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
    public function toggleUnit(Unit $unit)
    {
        $unit->is_active = !$unit->is_active;
        $unit->save();
        return redirect()->route('lokasi.unit.index')->with('success', 'unit Berhasil Di Non Aktifkan.');
    }
}
