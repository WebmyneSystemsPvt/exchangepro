<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $permissions = Permission::all();
            return DataTables::of($permissions)
                ->addColumn('actions', function ($permission) {
                    $editUrl = route('permissions.edit', $permission->id);
                    $deleteUrl = route('permissions.destroy', $permission->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    return <<<HTML
                        <a href="$editUrl" class="btn btn-warning btn-sm">Edit</a>
                        <form action="$deleteUrl" method="POST" style="display:inline-block;">
                            $csrf
                            $method
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    HTML;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('permissions.index');
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        Permission::create(['name' => $request->name]);
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully');
    }

    public function edit($id)
    {
        $permission = Permission::findById($id);
        return view('permissions.edit', compact('permission'));
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findById($id);
        $permission->name = $request->name;
        $permission->save();
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    }

    public function destroy($id)
    {
        $permission = Permission::findById($id);
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully');
    }
}
