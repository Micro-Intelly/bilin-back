<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\File;
use App\Models\Section;
use App\Models\Serie;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEpisodeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEpisodeRequest $request, Serie $serie, Section $section): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'max:500|nullable',
            'path' => 'required'
        ]);
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id))
        {
            if(Episode::check_limits($request)){
                $data = [
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'serie_id' => $serie->id,
                    'path' => '/app/'.$request->get('path'),
                    'type' => $serie->type,
                    'user_id' => $request->user()->id,
                    'section_id' => $section->id
                ];
                Episode::create($data);
                return response()->json(['status' => 200, 'message' => 'Created']);
            } else {
                return response()->json(['status' => 400, 'message' => 'Limit exceeded!']);
            }
        } else {
            abort(401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEpisodeRequest  $request
     * @param  \App\Models\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEpisodeRequest $request, Serie $serie, Section $section, Episode $episode): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'max:500|nullable',
        ]);
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id) &&
            $episode->section_id === $section->id &&
            $episode->serie_id === $serie->id)
        {
            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
            ];
            $episode->update($data);
            return response()->json(['status' => 200, 'message' => 'Updated']);
        } else {
            abort(401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Serie $serie,Section $section,Episode $episode): JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id) &&
            $episode->section_id === $section->id &&
            $episode->serie_id === $serie->id)
        {
            try {
                $episode->delete();
                return response()->json(['status' => 200, 'message' => 'Deleted']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }
    /**
     * Return streaming media source.
     *
     * @param  \App\Models\Episode  $episode
     * @return \Illuminate\Http\Response
     */
    public function stream(Episode $episode)
    {
        $stream = new VideoStreamController(storage_path() . $episode->path);
        $stream->start();exit;
    }
}
