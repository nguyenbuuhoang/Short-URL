<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPermission;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    // Lấy danh sách vai trò
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    // Lấy danh sách quyền
    public function getPermissions()
    {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    // Gán quyền cho vai trò
    public function assignPermission(AssignPermission $request)
    {

        $role = Role::find($request->role_id);
        $permission = Permission::find($request->permission_id);

        $role->givePermissionTo($permission);

        return response()->json('Permission assigned successfully');
    }

    // Xóa quyền khỏi vai trò
    public function revokePermission(AssignPermission $request)
    {
        $role = Role::find($request->role_id);
        $permission = Permission::find($request->permission_id);

        $role->revokePermissionTo($permission);

        return response()->json('Permission revoked successfully');
    }
}
