<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::with('permissions')->get();
            return DataTables::of($roles)
                ->addColumn('permissions', function ($role) {
                    return implode(', ', $role->permissions->pluck('name')->toArray());
                })
                ->addColumn('actions', function ($role) {
                    $editUrl = route('roles.edit', $role->id);
                    $deleteUrl = route('roles.destroy', $role->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    return <<<HTML
                        <a href="$editUrl" class="btn btn-warning btn-sm">Edit</a>
                        <form action="$deleteUrl" method="POST" style="display:inline-block;">
                            $csrf
                            $method
                            <button type="submit" class="btn btn-danger btn-sm delete-role">Delete</button>
                        </form>
                    HTML;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('roles.index');
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $role = Role::create(['name' => $request->name]);
        $request->validate([
            'permissions' => 'array'
        ]);

        $numericPermissionArray = [];
        if ($request->permissions) {
            foreach ($request->permissions as $permission) {
                $numericPermissionArray[] = intval($permission);
            }
        }
        $role->syncPermissions($numericPermissionArray);
        return redirect()->route('roles.index')->with('success', 'Role created successfully');
    }

    public function edit($id)
    {
        $role = Role::findById($id);
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        try {
            $request->validate([
                'permissions' => 'array'
            ]);

            $numericPermissionArray = [];
            if ($request->permissions) {
                foreach ($request->permissions as $permission) {
                    $numericPermissionArray[] = intval($permission);
                }
            }
            $role->syncPermissions($numericPermissionArray);

            return response()->json(['message' => 'Permissions updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating permissions: ' . $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        $role = Role::findById($id);
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully');
    }
}
