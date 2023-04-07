<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Http\Requests\StoreHistoryRequest;
use App\Http\Requests\UpdateHistoryRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_episodes(string $user): JsonResponse
    {
        return response()->json(
            History::with('history_able:id,title','serie:id,title')
                ->where('user_id','=',$user)
                ->where('history_able_type','=','App\Models\Episode')
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
            History::with('history_able:id,title')
                ->where('user_id','=',$user)
                ->where('history_able_type','=','App\Models\Post')
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
            History::with('history_able:id,title')
                ->where('user_id','=',$user)
                ->where('history_able_type','=','App\Models\Test')
                ->orderBy('updated_at','desc')
                ->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreHistoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHistoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\Response
     */
    public function show(History $history)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\Response
     */
    public function edit(History $history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHistoryRequest  $request
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHistoryRequest $request, History $history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\Response
     */
    public function destroy(History $history)
    {
        //
    }
}
