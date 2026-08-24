<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        return view('pages.roles.index');
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $query = Role::orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $perPage = (int) $request->get('per_page', 10);
        $roles = $query->paginate($perPage);

        $roles->setCollection($roles->getCollection()->map(function ($role) {
            if ($role->name === 'admin') {
                $action = '<span class="text-muted">-</span>';
            } else {
                $action = '<a href="' . route('roles.edit', $role) . '" class="btn btn-sm btn-warning me-1">Edit</a>';
                $action .= '<form action="' . route('roles.destroy', $role) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus role ini?\')">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>';
            }

            return [
                'id' => $role->id,
                'name' => $role->name,
                'action' => $action,
            ];
        }));

        return response()->json($roles);
    }

    public function create()
    {
        return view('pages.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        if ($role->name === 'admin') {
            abort(403);
        }

        return view('pages.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
        ]);

        $role->update(['name' => $request->name]);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            abort(403);
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}
