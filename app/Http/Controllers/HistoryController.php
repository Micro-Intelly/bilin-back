<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\History;
use App\Http\Requests\StoreHistoryRequest;
use App\Http\Requests\UpdateHistoryRequest;
use App\Models\Post;
use App\Models\Serie;
use App\Models\Test;
use Exception;
use Illuminate\Http\JsonResponse;

class HistoryController extends Controller
{
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
     * Display user posts
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
     * Display user tests
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
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreHistoryRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreHistoryRequest $request): JsonResponse
    {
        $this->validate($request, [
            'serie_id' => 'exists:series,id|nullable',
            'history_able_type' => 'required',
            'history_able_id' => 'required',
        ]);
        $classType = match ($request->get('history_able_type')) {
            'episode' => Episode::class,
            'post' => Post::class,
            'test' => Test::class,
            default => '',
        };
        if($classType == ''){
            return response()->json(['status' => 400, 'message'=>'Class type not exists']);
        }

        $recordExists = false;
        switch ($classType){
            case Episode::class : {
                $recordExists = Episode::where('id',$request->get('history_able_id'))->exists();
                break;
            }
            case Post::class : {
                $recordExists = Post::where('id',$request->get('history_able_id'))->exists();
                break;
            }
            case Test::class : {
                $recordExists = Test::where('id',$request->get('history_able_id'))->exists();
                break;
            }
        }
        if(! $recordExists){
            return response()->json(['status' => 400, 'message'=>'Record not exists']);
        }
        try {
            $data = [
              'user_id' => $request->user()->id,
              'serie_id' => $request->get('serie_id'),
              'history_able_id' => $request->get('history_able_id'),
              'history_able_type' => $classType,
            ];
            $oldHis = History::where('history_able_id', $request->get('history_able_id'))->first();
            $oldHis?->delete();
            History::create($data);
            return response()->json(['status' => 200, 'message' => 'Created']);
        } catch (Exception $exception) {
            return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
        }
    }
}
