<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Exports\UserListsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class UserListController extends Controller
{
    public function getListUser(Request $request)
    {
        $perPage = $request->input('perPage', 5);
        $sort_by = $request->input('sort_by', 'id');
        $sort_order = $request->input('sort_order', 'ASC');
        $name = $request->input('name');

        $query = User::with('totalUrls', 'roles')
            ->select('id', 'name', 'email', 'is_verified', 'created_at');

        if ($name) {
            $query->where('name', 'LIKE', "%$name%");
        }

        $query->orderBy($sort_by, $sort_order);
        if ($request->has('export') && $request->input('export') === 'csv') {
            $UserLists = $query->get();
            return Excel::download(new UserListsExport($UserLists), 'data_users.csv');
        }
        $users = $query->paginate($perPage);

        return response()->json(['users' => $users], 200);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $role = $request->input('role');
        $user->syncRoles([$role]);
        return response()->json(['message' => 'Vai trò của người dùng đã được cập nhật thành công'], 200);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Người dùng đã bị xóa thành công'], 200);
    }

    /*     public function deleteSelectedUsers(Request $request)
    {
        $userIds = $request->input('user_ids', []);

        if (empty($userIds)) {
            return response()->json(['message' => 'Không có người dùng nào được chọn'], 400);
        }

        try {
            User::whereIn('id', $userIds)->delete();
            return response()->json(['message' => 'Đã xóa thành công người dùng đã chọn'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi xóa người dùng'], 500);
        }
    } */
}
