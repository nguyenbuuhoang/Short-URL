<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ShortUrlController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::view('/', 'guest.home')->name('home');
});
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/verify', 'auth.verify')->name('verify');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::view('/links', 'user.links')->name('links');
});

Route::middleware(['auth:sanctum', 'role:admin|editor'])->prefix('admin')->group(function () {
    Route::view('/index', 'admin.index')->name('admin.index');
});

Route::get('/{shortCode}', [ShortUrlController::class, 'redirectToURL'])->name('shortcode');
