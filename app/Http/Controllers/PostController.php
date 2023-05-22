<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json(Post::with('author:id,name,email','language','tags')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
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
            TagController::tagControl($request, $post->id, Post::class);

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
     * @param \Illuminate\Http\Request $request
     * @param string $id
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

                TagController::tagControl($request, $id, Post::class);

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
     * @param \App\Models\Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request,Post $post): JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-post') ||
            $request->user()->id === $post->user_id))
        {
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
}
