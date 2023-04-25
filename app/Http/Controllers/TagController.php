<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Taggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json(Tag::orderBy('name')->get());
    }

    public static function tagControl(Request $request, string $id, string $class): void
    {
        $tags = $request->get('tags_id');
        $newTags = $request->get('new_tags');
        Taggable::select('tag_id')->where('taggable_id','=',$id)->delete();
        $taggablesToInsert = [];
        foreach($tags as $tag){
            $taggablesToInsert[] = [
                'tag_id' => $tag,
                'taggable_id' => $id,
                'taggable_type' => $class
            ];
        }
        Taggable::insert($taggablesToInsert);

        if(sizeof($newTags) > 0){
            $tagsToInsert = [];
            $now = Carbon::now('utc')->toDateTimeString();
            foreach($newTags as $tag){
                $tagsToInsert[] = [
                    'id' => Str::uuid()->toString(),
                    'name' => $tag,
                    'created_at'=> $now,
                    'updated_at'=> $now
                ];
            }
            Tag::insert($tagsToInsert);
            $insertRes = Tag::select('id')->whereIn('name', $newTags)->pluck('id');
            $taggablesToInsert2 = [];
            foreach($insertRes as $tagId){
                $taggablesToInsert2[] = [
                    'tag_id' => $tagId,
                    'taggable_id' => $id,
                    'taggable_type' => Post::class
                ];
            }
            Taggable::insert($taggablesToInsert2);
        }
    }
}
