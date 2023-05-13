<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Serie;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $test): JsonResponse
    {
        $testObj = Test::findOrFail($test);
        $validate = Test::validate_permission($request,$testObj);
        if($validate){
            $columns = ['id','question','answers'];
            if($request->user() != null &&
                (Test::find($test)->user_id == $request->user()->id ||
                $request->user()->can('manage-test')
                ))
            {
                $columns[] = 'correct_answer';
            }
            return response()->json(
                Question::select($columns)
                    ->where('test_id','=',$test)
                    ->get()
            );
        } else {
            abort(401);
        }
    }
}
