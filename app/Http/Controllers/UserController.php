<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Serie;
use App\Models\Test;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mews\Purifier\Facades\Purifier;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function showCurrentUser(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_series(string $user): JsonResponse
    {
        return response()->json(
            Serie::where('author_id','=',$user)
                ->orderBy('updated_at','desc')
                ->get());
    }
    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_posts(string $user): JsonResponse
    {
        return response()->json(
            Post::where('user_id','=',$user)
                ->orderBy('updated_at','desc')
                ->get());
    }
    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_tests(string $user): JsonResponse
    {
        return response()->json(
            Test::where('user_id','=',$user)
                ->orderBy('updated_at','desc')
                ->get());
    }
    /**
     * Display a listing of the resource.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_comments(string $user): JsonResponse
    {
        return response()->json(
            Comment::where('author_id','=',$user)
                ->orderBy('updated_at','desc')
                ->get());
    }

    public function update(Request $request, User $user):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-user') ||
            $request->user()->id === $user->id))
        {
            try {
                $this->validate($request, [
                    'name' => 'required|max:20',
                    'email' => 'required|max:100|email',
                ]);
                $user->update([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                ]);
                return response()->json(['status' => 200, 'message' => 'Updated']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    public function updateThumbnail(Request $request, User $user):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-user') ||
            $request->user()->id === $user->id))
        {
            try {
                $this->validate($request, [
                    'thumbnail' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:1024',
                ]);
                $image_path = $request->file('thumbnail')->store('image/user', 'public');
                $user->update([
                    'thumbnail' => 'storage/' . $image_path,
                ]);
                return response()->json(['status' => 200, 'message' => 'Updated']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    public function destroy(Request $request, User $user):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-user') ||
            $request->user()->id === $user->id
        )) {
            try {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $user->delete();
                return response()->json(['status' => 200, 'message' => 'Success']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }
}
