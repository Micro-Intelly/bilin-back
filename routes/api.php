<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SectionController;
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
Route::get('/user', [UserController::class,'index'])->name('user.index');
Route::get('/user/currentUser', [UserController::class,'showCurrentUser'])->name('user.showCurrentUser');
Route::get('/user/limits', [UserController::class,'getLimits'])->name('user.getLimits');
Route::patch('/user/{user}', [UserController::class,'update'])->name('user.update');
Route::post('/user/{user}/thumbnail', [UserController::class,'updateThumbnail'])->name('user.updateThumbnail');
Route::delete('/user/{user}', [UserController::class,'destroy'])->name('user.destroy');
Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

Route::get('/episode', [EpisodeController::class, 'testShowV'])->name('episode.index');
Route::get('/stream/{episode}', [EpisodeController::class, 'stream'])->name('episode.stream');
Route::get('/series', [SerieController::class, 'index'])->name('series.index');
Route::get('/series/{serie}', [SerieController::class, 'show'])->name('series.show');
Route::post('/series', [SerieController::class, 'store'])->name('series.store');
Route::post('/series/{serie}/thumbnail', [SerieController::class,'updateThumbnail'])->name('series.updateThumbnail');
Route::post('/series/{serie}/files', [FileController::class,'store'])->name('file.store');
Route::delete('/series/{serie}/files/{file}', [FileController::class,'destroy'])->name('file.destroy');

Route::post('/series/{serie}/sections', [SectionController::class,'store'])->name('section.store');
Route::patch('/series/{serie}/sections/{section}', [SectionController::class,'update'])->name('section.update');
Route::delete('/series/{serie}/sections/{section}', [SectionController::class,'destroy'])->name('section.destroy');

Route::post('/series/{serie}/sections/{section}/episodes', [EpisodeController::class,'store'])->name('episode.store');
Route::patch('/series/{serie}/sections/{section}/episodes/{episode}', [EpisodeController::class,'update'])->name('episode.update');
Route::delete('/series/{serie}/sections/{section}/episodes/{episode}', [EpisodeController::class,'destroy'])->name('episode.destroy');

Route::patch('/series/{serie}', [SerieController::class, 'update'])->name('series.update');
Route::delete('/series/{serie}', [SerieController::class, 'destroy'])->name('series.destroy');

Route::get('/posts', [PostController::class, 'index'])->name('post.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('post.show');
Route::post('/posts', [PostController::class, 'store'])->name('post.store');
Route::patch('/posts/{id}', [PostController::class, 'update'])->name('post.update');
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('post.destroy');
Route::get('/tests', [TestController::class, 'index'])->name('test.index');
Route::post('/tests', [TestController::class, 'store'])->name('test.store');
Route::patch('/tests/{test}', [TestController::class, 'update'])->name('test.update');
Route::delete('/tests/{test}', [TestController::class, 'destroy'])->name('test.destroy');
Route::get('/tests/{test}', [TestController::class, 'show'])->name('test.show');
Route::get('/tests/{test}/questions', [QuestionController::class, 'index'])->name('question.index');
Route::get('/tests/{test}/results', [TestController::class, 'showResultAverage'])->name('test.showResultAverage');
Route::post('/tests/{test}/questions', [TestController::class, 'updateQuestions'])->name('test.updateQuestions');
Route::post('/tests/{test}/answers', [TestController::class, 'postAnswer'])->name('test.postAnswer');
Route::get('/tags', [TagController::class, 'index'])->name('tag.index');
Route::get('/languages', [LanguageController::class, 'index'])->name('language.index');
Route::get('/comments/{id}', [CommentController::class, 'index'])->name('comments.index');
Route::post('/comments/{type}', [CommentController::class, 'store'])->name('comments.store');
Route::patch('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
Route::get('/user/{user}/series', [UserController::class, 'index_series'])->name('user.index.series');
Route::get('/user/{user}/organizations', [UserController::class, 'index_organizations'])->name('user.index.organizations');
Route::get('/user/{user}/posts', [UserController::class, 'index_posts'])->name('user.index.posts');
Route::get('/user/{user}/tests', [UserController::class, 'index_tests'])->name('user.index.test');
Route::get('/user/{user}/comments', [UserController::class, 'index_comments'])->name('user.index.comments');
Route::get('/user/{user}/histories/episodes', [HistoryController::class, 'index_episodes'])->name('history.index.episodes');
Route::get('/user/{user}/histories/posts', [HistoryController::class, 'index_posts'])->name('history.index.posts');
Route::get('/user/{user}/histories/tests', [HistoryController::class, 'index_tests'])->name('history.index.tests');
Route::post('/comment/image/upload', [CommentController::class, 'image_store'])->name('comment.image.store');

Route::get('/organization/{organization}/users', [OrganizationController::class, 'index_users'])->name('organization.index.users');
Route::post('/organization/{organization}/users', [OrganizationController::class, 'add_user'])->name('organization.add.user');
Route::delete('/organization/{organization}/users/{user}', [OrganizationController::class, 'delete_user'])->name('organization.delete.user');


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
