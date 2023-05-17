<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Question;
use App\Models\Result;
use App\Models\Serie;
use App\Models\Test;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use App\Models\User;
use Arr;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $test = Test::with('author:id,name,email','language','tags')
            ->withCount('questions')
            ->orderBy('updated_at','desc');
        if($request->user() == null) {
            $test = $test->where('access','=','public');
        } else if (! $request->user()->can('manage-test') ) {
            $userOrgs = User::where('id',$request->user()->id)
                ->with('organizations:id')
                ->first()->organizations->pluck('id');
            if($request->user()->organization_id != null){
                $userOrgs[] = $request->user()->organization_id;
            }
            $test = $test->whereIn('access',['public','registered'])
                ->orWhere(function ($q) use ($userOrgs){
                    $q->where('access', 'org')->whereIn('organization_id', $userOrgs);
                })
                ->orWhere('user_id', $request->user()->id);
        }
        return response()->json($test->get());
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
            'series_id' => 'exists:series,id|nullable',
            'organization_id' => 'exists:organizations,id|nullable'
        ]);

        try{
            if(Test::check_limits($request)) {
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
            } else {
                return response()->json(['status' => 400, 'message' => 'Limit exceeded!']);
            }
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
        $validate = Test::validate_permission($request,$test);
        if($validate){
            $query = Test::with('author:id,name,email','language','tags','serie:id,title','organization')->withCount('questions');
            if($request->user() != null){
                $userId = $request->user()->id;
                $query->with(['results' => function($query) use ($userId) {
                    $query->where('user_id', $userId);
                }]);
            }
            return response()->json($query->find($test->id));
        } else {
            abort(401);
        }
    }
    /**
     * Display the average of results
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_result_average(Test $test): JsonResponse
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
        if($request->user() != null &&
            ($request->user()->can('manage-test') ||
            $request->user()->id === $test->user_id))
        {
            $this->validate($request, [
                'title' => 'required|max:100',
                'description' => 'max:500|nullable',
                'language_id' => 'required|exists:languages,id',
                'access' => 'required',
                'level' => 'required',
                'series_id' => 'exists:series,id|nullable',
                'organization_id' => 'exists:organizations,id|nullable'
            ]);

            try {
                $access = $request->get('access');
                $level = $request->get('level');
                $organization_id = $request->get('organization_id');
                $language_id = $request->get('language_id');
                if ($request->get('series_id') != null) {
                    $serie = Serie::select(['access', 'level', 'language_id', 'organization_id'])
                        ->where('id', '=', $request->get('series_id'))
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
        } else {
            abort(401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTestRequest  $request
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function update_questions(UpdateTestRequest $request, Test $test): JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-test') ||
            $request->user()->id === $test->user_id)) {
            $this->validate($request, [
                'upsert.*.question' => 'required|max:500',
                'upsert.*.correct_answer' => 'required',
                'upsert.*.answers' => 'required',
                'destroy.*' => 'required|uuid',
            ]);
            try {
                $recordReceived = $request->get('upsert');
                $recordToUpsert = [];
                $recordToCheckOwnership = [];
                $recordToCheckOwnershipIds = [];
                if (count($recordReceived) > 0) {
                    foreach ($recordReceived as $question) {
                        $question['test_id'] = $test->id;
                        $question['answers'] = json_encode($question['answers']);
                        if ($question['id'] == '') {
                            $question['id'] = (string)Str::orderedUuid();
                            $recordToUpsert[] = $question;
                        } else {
                            $recordToCheckOwnershipIds[] = $question['id'];
                            $recordToCheckOwnership[] = $question;
                        }
                    }

                    if (!$request->user()->can('manage-test')) {
                        $queryRes = Question::with('test')
                            ->whereIn('id', $recordToCheckOwnershipIds)
                            ->whereHas('test', function ($q) use ($request) {
                                $q->where('user_id', $request->user()->id);
                            })
                            ->get()->pluck('id');
                        if (count($queryRes) == count($recordToCheckOwnershipIds)) {
                            $recordToUpsert = $recordToUpsert + $recordToCheckOwnership;
                        } else {
                            $recordFiltered = Arr::where($recordToCheckOwnership, function ($value, $key) use ($queryRes) {
                                return in_array($value['id'], (array)$queryRes);
                            });
                            $recordToUpsert = $recordToUpsert + $recordFiltered;
                        }
                    } else {
                        $recordToUpsert = $recordToUpsert + $recordToCheckOwnership;
                    }
                    Question::upsert($recordToUpsert, 'id');
                }

                Question::with('test')
                    ->whereIn('id', $request->get('destroy'))
                    ->whereHas('test', function ($q) use ($request) {
                        $q->where('user_id', $request->user()->id);
                    })
                    ->delete();

                return response()->json(['status' => 200, 'message' => 'Updated']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Test  $test
     * @return \Illuminate\Http\Response
     */
    public function post_answer(Request $request, Test $test): JsonResponse
    {
        $this->validate($request, [
            'answers.*.qid' => 'required|exists:questions,id',
        ]);
        if(!Test::validate_permission($request, $test)){
            abort(401);
        }
        try {
            $recordReceived = $request->get('answers');
            $questionIds = [];
            foreach ($recordReceived as $answer) {
                $questionIds[] = $answer['qid'];
            }
            $questions = Question::with('test')
                ->whereIn('id', $questionIds)
                ->where('test_id', $test->id)
                ->get();
            $size = count($questions);
            $correctAnswer = 0;
            foreach ($recordReceived as $answer) {
                if($answer['selectedAnswer'] > 0){
                    $question = $questions->firstWhere('id', $answer['qid']);
                    if($question != null && $question->correct_answer == $answer['selectedAnswer']) {
                        $correctAnswer += 1;
                    }
                }
            }
            $score = ($correctAnswer/$size)*10;

            if($request->user() != null){
                $resultCount = Result::where('user_id', '=', $request->user()->id)
                    ->where('test_id', '=', $test->id)
                    ->count();
                if($resultCount < 10){
                    Result::create([
                        'n_try' => $resultCount + 1,
                        'result' => $score,
                        'user_id' => $request->user()->id,
                        'test_id' => $test->id
                    ]);
                }
            }

            return response()->json(['status' => 200, 'message' => 'Success', 'score' => $score]);
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
