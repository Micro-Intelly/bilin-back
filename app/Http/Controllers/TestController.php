<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Result;
use App\Models\Test;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
//        $series = ($request->user() != null)
        return response()
            ->json(Test::with('author:id,name,email','language','tags')
                ->withCount('questions')
                ->orderBy('updated_at','desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTestRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request,Test $test)
    {
        $query = Test::with('author:id,name,email');
        if($request->user() != null){
            $userId = $request->user()->id;
            $query->with(['results' => function($query) use ($userId) {
                $query->where('user_id', $userId);
            }]);
        }
        return response()->json($query->find($test->id));
    }
    /**
     * Display the average of results
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\JsonResponse
     */
    public function showResultAverage(Test $test)
    {
        return response()->json(Result::selectRaw('n_try,AVG(result) as avg_result')
            ->where('test_id','=',$test->id)
            ->groupBy('n_try')
            ->orderBy('n_try')
            ->get());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function edit(Test $test)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTestRequest  $request
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTestRequest $request, Test $test)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function destroy(Test $test)
    {
        //
    }
}
