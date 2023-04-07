<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Models\Tag;
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

Route::get('/isLoggedIn', [LoginController::class,'isLoggedIn'])->name('user.check');
Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
Route::get('/episode', [EpisodeController::class, 'testShowV'])->name('episode.index');
Route::get('/stream/{episode}', [EpisodeController::class, 'stream'])->name('episode.stream');
Route::get('/series', [SerieController::class, 'index'])->name('series.index');
Route::get('/series/{serie}', [SerieController::class, 'show'])->name('series.show');
Route::get('/posts', [PostController::class, 'index'])->name('post.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('post.show');
Route::get('/tests', [TestController::class, 'index'])->name('test.index');
Route::get('/tests/{test}', [TestController::class, 'show'])->name('test.show');
Route::get('/tests/{test}/questions', [QuestionController::class, 'index'])->name('question.index');
Route::get('/tests/{test}/results', [TestController::class, 'showResultAverage'])->name('test.showResultAverage');
Route::get('/tags', [TagController::class, 'index'])->name('tag.index');
Route::get('/languages', [LanguageController::class, 'index'])->name('language.index');
Route::get('/comments/{id}', [CommentController::class, 'index'])->name('comments.index');
Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
Route::get('/user/{user}/series', [UserController::class, 'index_series'])->name('user.index.series');
Route::get('/user/{user}/posts', [UserController::class, 'index_posts'])->name('user.index.posts');
Route::get('/user/{user}/tests', [UserController::class, 'index_tests'])->name('user.index.test');
Route::get('/user/{user}/comments', [UserController::class, 'index_comments'])->name('user.index.comments');
Route::get('/user/{user}/histories/episodes', [HistoryController::class, 'index_episodes'])->name('history.index.episodes');
Route::get('/user/{user}/histories/posts', [HistoryController::class, 'index_posts'])->name('history.index.posts');
Route::get('/user/{user}/histories/tests', [HistoryController::class, 'index_tests'])->name('history.index.tests');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::middleware(['permission:manage-self-user'])->get('/users/{user}', [UserController::class, 'show'])->name('user.show');
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
