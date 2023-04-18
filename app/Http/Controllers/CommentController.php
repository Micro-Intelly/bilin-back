<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Episode;
use App\Models\Post;
use App\Models\Serie;
use App\Models\Test;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, string $id): JsonResponse
    {
        return response()
            ->json(
                Comment::where('commentable_id', $id)
                    ->where('root_comm_id')
                    ->with(
                    'author:id,name,email',
                    'comments.author:id,name,email')
                    ->orderBy('created_at','desc')
                    ->get()
            );
    }

    /**
     * Store the image posted on note.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function image_store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $image_path = $request->file('image')->store('image/note', 'public');

        return response()->json(['imageUrl' => request()->root() . '/storage/' . $image_path]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCommentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCommentRequest $request, string $type): JsonResponse
    {
        $body = '';
        if($type == 'comment'){
            $this->validate($request, [
                'body' => 'required|max:500',
                'commentable_type' => 'required',
            ]);
            $body = $request->get('body');
        } else if ($type == 'note'){
            $this->validate($request, [
                'body' => 'required|max:10000',
                'commentable_type' => 'required',
            ]);
            $body = Purifier::clean($request->get('body'),[
                'HTML.Allowed' => 'b,strong[style],i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[style|width|height|alt|src],span,br,h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],blockquote,div[style],font[size|color],blockquote[style],sub[style],sup[style],strike[style]',
                'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,max-width,border,width',
                'AutoFormat.AutoParagraph' => true,
                'AutoFormat.RemoveEmpty' => true,
                'CSS.MaxImgLength' => null,
            ]);
        } else {
            return response()->json(['status' => 404, 'message'=>'Endpoint not found']);
        }
        $classType = match ($request->get('commentable_type')) {
            'episode' => Episode::class,
            'serie' => Serie::class,
            'post' => Post::class,
            'test' => Test::class,
            default => '',
        };

        Comment::create([
            'body' => $body,
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'author_id' => $request->user()->id,
            'in_reply_to_id' => $request->get('in_reply_to_id'),
            'root_comm_id' => $request->get('root_comm_id'),
            'type' => $type,
            'serie_id' => $request->get('serie_id'),
            'commentable_id' => $request->get('commentable_id'),
            'commentable_type' => $classType,
        ]);

        return response()->json(['status' => 200, 'message'=>'Saved '.$type.' successfully']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCommentRequest  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCommentRequest $request, string $id):JsonResponse
    {
        $comment = Comment::findOrFail($id);
        if($request->user() != null &&
            ($request->user()->can('manage-comment') ||
            $request->user()->id === $comment->author_id))
        {
            try {
                switch ($comment->type){
                    case 'comment': {
                        $this->validate($request, [
                            'body' => 'required|max:2048',
                        ]);
                        $comment->update(['body' => $request->get('body')]);
                        break;
                    }
                    case 'note': {
                        $this->validate($request, [
                            'body' => 'required|max:10000',
                        ]);
                        $body = Purifier::clean($request->get('body'),[
                            'HTML.Allowed' => 'b,strong[style],i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[style|width|height|alt|src],span,br,h1[style],h2[style],h3[style],h4[style],h5[style],h6[style],blockquote,div[style],font[size|color],blockquote[style],sub[style],sup[style],strike[style]',
                            'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,max-width,border,width',
                            'AutoFormat.AutoParagraph' => true,
                            'AutoFormat.RemoveEmpty' => true,
                            'CSS.MaxImgLength' => null,
                        ]);
                        $comment->update([
                            'title' => $request->get('title'),
                            'description' => $request->get('description'),
                            'body' => $body
                        ]);
                        break;
                    }
                }
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
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, string $id):JsonResponse
    {
        $comment = Comment::findOrFail($id);
        if($request->user() != null &&
            ($request->user()->can('manage-comment') ||
            $request->user()->id === $comment->author_id
        )) {
            try {
                Comment::destroy($id);
                return response()->json(['status' => 200, 'message' => 'Success']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }
}
