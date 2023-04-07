<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Serie;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }
    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_series(string $user): JsonResponse
    {
        return response()->json(
            Serie::where('author_id','=',$user)
                ->orderBy('updated_at','desc')
                ->get());
    }
    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_posts(string $user): JsonResponse
    {
        return response()->json(
            Post::where('user_id','=',$user)
                ->orderBy('updated_at','desc')
                ->get());
    }
    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_tests(string $user): JsonResponse
    {
        return response()->json(
            Test::where('user_id','=',$user)
                ->orderBy('updated_at','desc')
                ->get());
    }
    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_comments(string $user): JsonResponse
    {
        return response()->json(
            Comment::where('author_id','=',$user)
                ->orderBy('updated_at','desc')
                ->get());
    }
}
