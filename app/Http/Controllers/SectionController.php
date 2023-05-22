<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Requests\StoreSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use App\Models\Serie;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\StoreSectionRequest $request
     * @param Serie $serie
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(StoreSectionRequest $request, Serie $serie): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'description' => 'max:500|nullable',
        ]);

        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id))
        {
            try {
                $data = [
                    'name' => $request->get('name'),
                    'description' => $request->get('description'),
                    'series_id' => $serie->id,
                ];
                Section::create($data);

                return response()->json(['status' => 200, 'message' => 'Created']);
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
     * @param \App\Http\Requests\UpdateSectionRequest $request
     * @param Serie $serie
     * @param \App\Models\Section $section
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UpdateSectionRequest $request, Serie $serie, Section $section): JsonResponse
    {
        $this->validate($request, [
            'name' => 'required|max:100',
            'description' => 'max:500|nullable',
        ]);

        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id) &&
            $section->series_id === $serie->id)
        {
            try {
                $data = [
                    'name' => $request->get('name'),
                    'description' => $request->get('description'),
                ];
                $section->update($data);

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
     * @param Request $request
     * @param Serie $serie
     * @param \App\Models\Section $section
     * @return JsonResponse
     */
    public function destroy(Request $request, Serie $serie, Section $section): JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-series') ||
            $request->user()->id === $serie->author_id) &&
            $section->series_id === $serie->id)
        {
            try {
                $section->delete();

                return response()->json(['status' => 200, 'message' => 'Deleted']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }

        } else {
            abort(401);
        }
    }
}
