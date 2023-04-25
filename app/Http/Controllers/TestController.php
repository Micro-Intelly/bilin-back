<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Result;
use App\Models\Serie;
use App\Models\Test;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTestRequest $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'max:500|nullable',
            'language_id' => 'required|exists:languages,id',
            'access' => 'required',
            'level' => 'required',
            'series_id' => 'exists:series,id|nullable'
        ]);

        try{
            $access = $request->get('access');
            $level = $request->get('level');
            $organization_id = $request->get('organization_id');
            $language_id = $request->get('language_id');
            if($request->get('series_id') != null){
                $serie = Serie::select(['access','level','language_id','organization_id'])
                    ->where('id','=',$request->get('series_id'))
                    ->first();

                $access = $serie->access;
                $level = $serie->level;
                $organization_id = $serie->organization_id;
                $language_id = $serie->language_id;
            }

            $test = Test::create([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'user_id' => $request->user()->id,
                'series_id' => $request->get('series_id'),
                'language_id' => $language_id,
                'access' => $access,
                'level' => $level,
                'organization_id' => $organization_id,
            ]);
            TagController::tagControl($request, $test->id, Test::class);

            return response()->json(['status' => 200, 'message' => 'Created']);
        } catch (Exception $exception) {
            return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request,Test $test)
    {
        $query = Test::with('author:id,name,email','language','tags','serie:id,title','organization');
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
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTestRequest  $request
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTestRequest $request, Test $test): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'max:500|nullable',
            'language_id' => 'required|exists:languages,id',
            'access' => 'required',
            'level' => 'required',
            'series_id' => 'exists:series,id|nullable'
        ]);

        try{
            $access = $request->get('access');
            $level = $request->get('level');
            $organization_id = $request->get('organization_id');
            $language_id = $request->get('language_id');
            if($request->get('series_id') != null){
                $serie = Serie::select(['access','level','language_id','organization_id'])
                    ->where('id','=',$request->get('series_id'))
                    ->first();

                $access = $serie->access;
                $level = $serie->level;
                $organization_id = $serie->organization_id;
                $language_id = $serie->language_id;
            }

            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'user_id' => $request->user()->id,
                'series_id' => $request->get('series_id'),
                'language_id' => $language_id,
                'access' => $access,
                'level' => $level,
                'organization_id' => $organization_id,
            ];
            $test->update($data);
            TagController::tagControl($request, $test->id, Test::class);

            return response()->json(['status' => 200, 'message' => 'Updated']);
        } catch (Exception $exception) {
            return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, Test $test):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-test') ||
            $request->user()->id === $test->user_id))
        {
            try {
                $test->delete();
                return response()->json(['status' => 200, 'message' => 'Success']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }
}
