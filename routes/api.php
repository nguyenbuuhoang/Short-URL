<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\ShortUrlController;
use App\Http\Controllers\Admin\MakeRoleController;
use App\Http\Controllers\Admin\UserListController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ShortLinksController;
use App\Http\Controllers\Guest\ShortUrlGuestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//verify link
Route::post('/verify/{id}', [AuthController::class, 'verify']);
Route::post('/resend-verification/{id}', [AuthController::class, 'resendVerificationEmail']);

//create short for guest
Route::post('guest/create-short-url', [ShortUrlGuestController::class, 'createShortURL'])->middleware('guest');

//middleware sanctum
Route::middleware('auth:sanctum')->group(function () {
    //Get User
    Route::get('user', [AuthController::class, 'user']);
    //Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    //Get short for user
    Route::get('/user-short-urls', [ShortURLController::class, 'getUserShortUrls']);
    Route::get('/short-urls/{user_id}', [ShortURLController::class, 'getShortURLsByUserId']);
    Route::get('short-urls/{user_id}/totals', [ShortURLController::class, 'getTotalsByUserId']);
    //CRUD short for User
    Route::post('user/create-short-url', [ShortUrlController::class, 'createShortURL']);
    Route::put('/short-urls/{id}', [ShortURLController::class, 'updateShortCode']);
    Route::delete('/short-urls/{id}', [ShortURLController::class, 'deleteShortURL']);
});

//Admin ListUser
Route::get('/users-list', [UserListController::class, 'getListUser']);
Route::put('/users/{id}', [UserListController::class, 'updateUser']);
Route::delete('/users/{id}', [UserListController::class, 'deleteUser']);
//Route::delete('delete-selected-users', [UserListController::class, 'deleteSelectedUsers']);
Route::get('/totals', [ShortLinksController::class, 'getTotal']);
//Admin ShortURL
Route::get('/shortURL', [ShortLinksController::class, 'getShortURL']);
Route::put('/shortURL/{id}', [ShortLinksController::class, 'updateShortURL']);
Route::delete('/shortURL/{id}', [ShortLinksController::class, 'deleteShortURL']);
Route::get('/shortURL/qrcode/{id}', [ShortLinksController::class, 'getQRCode']);
//Admin Permission
Route::get('/roles', [PermissionController::class, 'getRoles']);
Route::get('/permissions', [PermissionController::class, 'getPermissions']);
Route::post('/assign_permission', [PermissionController::class, 'assignPermission']);
Route::post('/revoke_permission', [PermissionController::class, 'revokePermission']);

//create role and permission
Route::get('addrole', [MakeRoleController::class, 'addRole']);
Route::get('addpermission/user', [MakeRoleController::class, 'addPermissionUser']);
Route::get('addpermission/link', [MakeRoleController::class, 'addPermissionLink']);
Route::get('role/permission/user', [MakeRoleController::class, 'roleAsPermissionUser']);
Route::get('role/permission/link', [MakeRoleController::class, 'roleAsPermissionLink']);
