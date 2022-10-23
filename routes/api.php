<?php

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

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::middleware(['role:Admin|Student', 'permission:view-user'])->get('/user/viewTest', function (Request $request) {
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
