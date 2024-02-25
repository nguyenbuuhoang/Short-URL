<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Exports\UserListsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\DataProcessorService;
use App\Services\User\ShortUrlService;

class UserListController extends Controller
{
    protected $shortUrlService;
    protected $dataProcessorService;
    protected $userService;
    public function __construct(
        ShortUrlService $shortUrlService,
        DataProcessorService $dataProcessorService,
        UserService $userService
    ) {
        $this->shortUrlService = $shortUrlService;
        $this->dataProcessorService = $dataProcessorService;
        $this->userService = $userService;
    }
    public function getListUser(Request $request)
    {
        $perPage = $request->input('perPage', 5);
        $sort_by = $request->input('sort_by', 'id');
        $sort_order = $request->input('sort_order', 'ASC');
        $name = $request->input('name');
        $export = $request->input('export');
        $query = User::with('totalUrls', 'roles')
            ->select('id', 'name', 'email', 'is_verified', 'created_at');

        if ($name) {
            $query = $this->dataProcessorService->filterByName($query, $name);
        }
        $query = $this->dataProcessorService->sort($query, $sort_by, $sort_order);
        if ($export === 'csv') {
            return Excel::download(new UserListsExport($query->get()), 'data_users.csv');
        }
        $users = $this->dataProcessorService->paginate($query, $perPage);

        return response()->json(['users' => $users], 200);
    }

    public function updateUser(Request $request, $id)
    {
        $user =  $this->userService->findUser($id);
        $role = $request->input('role');
        $user->syncRoles([$role]);
        return response()->json(['message' => 'The users role has been successfully updated'], 200);
    }

    public function deleteUser($id)
    {
        $user =  $this->userService->findUser($id);
        $user->delete();
        return response()->json(['message' => 'The user has been successfully deleted'], 200);
    }
}
