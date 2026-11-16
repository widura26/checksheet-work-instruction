<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Departemen;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartemenController extends Controller
{
    public function index()
    {
        return view('pages.lokasi.departemen.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Departemen::query();

        if ($request->filled('search')) {
            $query->where('nama_departemen', 'like', '%' . $request->search . '%');
        }

        $perPage = (int) $request->get('per_page', 10);
        $departemens = $query->paginate($perPage);

        $departemens->setCollection($departemens->getCollection()->map(function ($departemen) {
            $buttons = '<div class="d-flex justify-content-center gap-2" role="group">';
            if (Auth::user()->hasRole(['admin'])) {
                $buttons .= '
                <form action="' . route('lokasi.departemen.toggle', $departemen->id) . '" method="POST">
                    ' . csrf_field() . '
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" role="switch"
                            onchange="this.form.submit()" ' . ($departemen->is_active == true ? 'checked' : '') . '>
                    </div>
                </form>
            ';
            }
            $buttons .= '<a href="' . route('lokasi.departemen.edit', $departemen->id) . '" class="btn btn-primary btn-sm">Edit</a>';

            return [
                'id' => $departemen->id,
                'nama_departemen' => $departemen->nama_departemen,
                'action' => $buttons,
            ];
        }));

        return response()->json($departemens);
    }

    public function create()
    {
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        return view('pages.lokasi.departemen.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
        ]);

        Departemen::create($request->all());

        return redirect()->route('lokasi.departemen.index')->with('success', 'Departemen created successfully.');
    }

    public function edit($id){
        if (!Auth::user()->hasRole(['admin'])) {
            abort(403);
        }
        $departemen = Departemen::findOrFail($id);

        return view('pages.lokasi.departemen.edit', compact('departemen'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'nama_departemen' => 'required|string|max:255',
        ]);

        $departemen = Departemen::findOrFail($id);
        $departemen->update($request->all());

        return redirect()->route('lokasi.departemen.index')->with('success', 'Departemen updated successfully.');
    }

    public function destroy($id)
    {
        Departemen::findOrFail($id)->delete();

        return redirect()->route('lokasi.departemen.index')->with('success', 'Departemen deleted successfully.');
    }

    public function toggleDepartemen(Departemen $departemen){
        $departemenActive = $departemen->is_active;
        $departemen->is_active = !$departemen->is_active;
        $departemen->save();
        Bagian::where('departemen_id', $departemen->id)
            ->update(['is_active' => !$departemenActive]);
        Unit::whereHas('bagian', function ($q) use ($departemen) {
            $q->where('departemen_id', $departemen->id);
        })->update(['is_active' => !$departemenActive]);

        return redirect()->route('lokasi.departemen.index')->with('success', 'Departemen Berhasil Di Non Aktifkan.');
    }
}
