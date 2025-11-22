<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:AccessManagement-Index|AccessManagement-Create|AccessManagement-Edit|AccessManagement-Delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:AccessManagement-Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:AccessManagement-Edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:AccessManagement-Delete', ['only' => ['destroy']]);
        $this->middleware('permission:AccessManagement-View', ['only' => ['show']]);
    }


    public function index(Request $request): View
    {
        $roles = Role::orderBy('id', 'DESC')->get();
        return view('roles.index', compact('roles'));
    }


    public function create(): View
    {
        $permission = Permission::get();
        return view('roles.create', compact('permission'));
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
        ]);


        $role = Role::create(['name' => $validated['name']]);

        $role->syncPermissions($validated['permission']);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    public function show($id): View
    {

        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();

        return view('roles.show', compact('role', 'rolePermissions'));
    }


    public function edit($id): View
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.edit', compact('role', 'permission', 'rolePermissions'));
    }


    public function update(Request $request, $id): RedirectResponse
    {
        $validate = $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);

        // dd($validate);
        $role = Role::find($id);
        $role->name = $validate['name'];
        $role->save();

        $role->syncPermissions($validate['permission']);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $Role = Role::find($id);
        $Role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
}
