<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\MakeRoleController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


//create role and permission
Route::get('addrole', [MakeRoleController::class, 'addRole']);
Route::get('addpermission/user', [MakeRoleController::class, 'addPermissionUser']);
Route::get('addpermission/link', [MakeRoleController::class, 'addPermissionLink']);
Route::get('role/permission/user', [MakeRoleController::class, 'roleAsPermissionUser']);
Route::get('role/permission/link', [MakeRoleController::class, 'roleAsPermissionLink']);
