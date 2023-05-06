<?php

namespace App\Http\Controllers;

use App\Models\Serie;
use App\Http\Requests\StoreSerieRequest;
use App\Http\Requests\UpdateSerieRequest;
use App\Models\Test;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SerieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
//        $series = ($request->user() != null)
        return response()->json(
            Serie::with('author:id,name,email', 'language','tags','organization')
                ->orderBy('updated_at', 'desc')
                ->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSerieRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSerieRequest $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'required|max:500',
            'language_id' => 'required|exists:languages,id',
            'access' => 'required',
            'level' => 'required',
            'type' => 'required',
            'organization_id' => 'exists:organizations,id|nullable'
        ]);

        try{
            $serie = Serie::create([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'author_id' => $request->user()->id,
                'language_id' => $request->get('language_id'),
                'access' => $request->get('access'),
                'level' => $request->get('level'),
                'organization_id' => $request->get('organization_id'),
                'type' => $request->get('type'),
            ]);
            TagController::tagControl($request, $serie->id, Serie::class);

            return response()->json(['status' => 200, 'message' => 'Created']);
        } catch (Exception $exception) {
            return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Serie  $serie
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        return response()->json(Serie::with(
            'author:id,name,email','language','tags', 'tests',
            'sections.episodes.author', 'comments', 'organization','files'
        )->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSerieRequest  $request
     * @param  \App\Models\Serie  $serie
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSerieRequest $request, Serie $serie):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id))
        {
            $this->validate($request, [
                'title' => 'required|max:100',
                'description' => 'max:500|nullable',
                'language_id' => 'required|exists:languages,id',
                'access' => 'required',
                'level' => 'required',
                'type' => 'required',
                'organization_id' => 'exists:organizations,id|nullable'
            ]);

            try {
                $data = [
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'user_id' => $request->user()->id,
                    'language_id' => $request->get('language_id'),
                    'access' => $request->get('access'),
                    'level' => $request->get('level'),
                    'organization_id' => $request->get('organization_id'),
                ];
                $serie->update($data);
                TagController::tagControl($request, $serie->id, Serie::class);

                return response()->json(['status' => 200, 'message' => 'Updated']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    public function updateThumbnail(Request $request, Serie $serie):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id))
        {
            try {
                $this->validate($request, [
                    'thumbnail' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:1024',
                ]);
                $image_path = $request->file('thumbnail')->store('image/series', 'public');
                $serie->update([
                    'image' => 'storage/' . $image_path,
                ]);
                return response()->json(['status' => 200, 'message' => 'Updated']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Serie  $serie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Serie $serie): JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id))
        {
            try {
                $serie->delete();
                return response()->json(['status' => 200, 'message' => 'Success']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }
}
