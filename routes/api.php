<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/signup', [RegisterController::class, 'signup'])->name('signup');

Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
Route::get('/video', [PostController::class, 'testShowV'])->name('video.show');
Route::get('/podcast', [PostController::class, 'testShowP'])->name('podcast.show');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::middleware(['role:Admin|Student', 'permission:view-user'])->get('/user', function (Request $request) {
        return $request->user();
    });
    Route::middleware(['role:Admin|Student', 'permission:delete-user'])->get('/user/deleteTest', function (Request $request) {
        return $request->user();
    });
    Route::middleware(['role:Admin'])->get('/user/adminTest', function (Request $request) {
        return $request->user();
    });
});

Route::get('/postTest/{id}',[PostController::class, 'testGet']);
Route::get('/userCan', function (Request $request) {
    return response()->json([
        "view" => $request->user()->can('view-user'),
        "delete" => $request->user()->can('delete-user'),
        "publish" => $request->user()->can('publish-post'),
        "create" => $request->user()->can('create-user'),
    ]);
});
