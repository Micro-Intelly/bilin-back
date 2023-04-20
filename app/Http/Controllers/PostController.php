<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Models\Comment;
use App\Models\Episode;
use App\Models\Post;
use App\Models\Serie;
use App\Models\Tag;
use App\Models\Taggable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
//        $series = ($request->user() != null)
        return response()->json(Post::with('author:id,name,email','language','tags')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'body' => 'required|max:10000',
            'language_id' => 'required|exists:languages,id'
        ]);

        try{
            $body = Purifier::clean($request->get('body'),[
                'HTML.Allowed' => 'b,strong[style],i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[style|width|height|alt|src],span,br,h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],blockquote,div[style],font[size|color],blockquote[style],sub[style],sup[style],strike[style]',
                'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,max-width,border,width',
                'AutoFormat.AutoParagraph' => true,
                'AutoFormat.RemoveEmpty' => true,
                'CSS.MaxImgLength' => null,
            ]);
            $post = Post::create([
                'title' => $request->get('title'),
                'body' => $body,
                'user_id' => $request->user()->id,
                'language_id' => $request->get('language_id'),
            ]);
            $this->tagControl($request, $post->id);

            return response()->json(['status' => 200, 'message' => 'Created']);
        } catch (Exception $exception) {
            return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Post $post)
    {
        $post->load('author:id,name,email','language','tags');
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $post = Post::findOrFail($id);
        if($request->user() != null &&
            ($request->user()->can('manage-post') ||
            $request->user()->id === $post->user_id))
        {
            try {
                $this->validate($request, [
                    'title' => 'required:max:100',
                    'body' => 'required|max:10000',
                    'language_id' => 'required'
                ]);
                $body = Purifier::clean($request->get('body'),[
                    'HTML.Allowed' => 'b,strong[style],i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[style|width|height|alt|src],span,br,h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],blockquote,div[style],font[size|color],blockquote[style],sub[style],sup[style],strike[style]',
                    'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,max-width,border,width',
                    'AutoFormat.AutoParagraph' => true,
                    'AutoFormat.RemoveEmpty' => true,
                    'CSS.MaxImgLength' => null,
                ]);
                $post->update([
                    'title' => $request->get('title'),
                    'language_id' => $request->get('language_id'),
                    'body' => $body
                ]);

                $this->tagControl($request, $id);

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
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request,Post $post): JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-post') ||
            $request->user()->id === $post->user_id
        )) {
            try {
                $post->delete();
                return response()->json(['status' => 200, 'message' => 'Success']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    private function tagControl(Request $request, string $id): void
    {
        $tags = $request->get('tags_id');
        $newTags = $request->get('new_tags');
        Taggable::select('tag_id')->where('taggable_id','=',$id)->delete();
        $taggablesToInsert = [];
        foreach($tags as $tag){
            $taggablesToInsert[] = [
                'tag_id' => $tag,
                'taggable_id' => $id,
                'taggable_type' => Post::class
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
