<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Org_user;
use App\Models\Organization;
use App\Models\Post;
use App\Models\Serie;
use App\Models\Test;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::with('organization')->orderBy('email')->get();
        $resUser = $users->filter(function (User $value) {
            return !str_contains($value->getRoleNames()->join(','),'Admin');
        });
        return response()->json($resUser->values()->toArray());
    }

    /**
     * Get current user data.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_current_user(Request $request): JsonResponse
    {
        if($request->user() != null){
            $user = User::with('organizations','organization')->findOrFail($request->user()->id);
            return UserController::get_user_data($user);
        } else {
            abort(401,'No user is logged');
        }
    }

    /**
     * Get application limits.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_limits(Request $request): JsonResponse
    {
        return response()->json(config('constants.limits'));
    }

    /**
     * Display user series.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_series(string $user): JsonResponse
    {
        return response()->json(
            Serie::where('author_id','=',$user)
                ->with('language','organization')
                ->orderBy('updated_at','desc')
                ->get());
    }
    /**
     * Display user organizations.
     *
     * @param  string  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index_organizations(string $user): JsonResponse
    {
        return response()->json(
            Organization::whereHas('users',function($q) use($user) {
                    $q->where('id','=', $user);
                })
                ->orderBy('name')
                ->get());
    }

    /**
     * Display user posts.
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
     * Display user tests.
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
     * Display user comments.
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

    /**
     * Update user data
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-user') ||
            $request->user()->id === $user->id))
        {
            try {
                $this->validate($request, [
                    'name' => 'required|max:50',
                    'email' => 'required|max:100|email',
                ]);
                if($user->organization_id != null){
                    $this->validate($request, [
                        'orgName' => 'required|max:100',
                    ]);
                }
                $user->update([
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                ]);
                if($user->organization_id != null){
                    $user->organization()->update([
                        'name' => $request->get('orgName'),
                        'description' => $request->get('orgDescription'),
                    ]);
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
     * Update user thumbnail
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update_thumbnail(Request $request, User $user):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('manage-user') ||
            $request->user()->id === $user->id))
        {
            try {
                $this->validate($request, [
                    'thumbnail' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:1024',
                ]);
                if($user->thumbnail != null && User::where('thumbnail', $user->thumbnail)->count() < 2){
                    Storage::disk('do-spaces')->delete($user->thumbnail);
                }
                $image_path = $request->file('thumbnail')->store('public/image/user', 'do-spaces');

                $user->update([
                    'thumbnail' => $image_path,
                ]);
                return response()->json(['status' => 200, 'message' => $image_path]);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    /**
     * Delete user
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(Request $request, User $user):JsonResponse
    {
        if($request->user() != null &&
            ($request->user()->can('delete-user') ||
            $request->user()->id === $user->id))
        {
            try {
                $user->delete();

                if($request->user()->id === $user->id){
                    auth('api')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return response()->json(['status' => 200, 'message' => 'Success']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    /**
     * Get user data with its relations
     *
     * @param User $user
     * @return JsonResponse
     */
    public static function get_user_data(User $user): JsonResponse
    {
        $res = $user->toArray() + [
                'role' => $user->getRoleNames()->join(','),
                'org_count' => Org_user::where('user_id', '=', $user->id)->count(),
                'orgs' => Organization::select('name')->whereHas('users', function ($q) use ($user) {
                    $q->where('id', '=', $user->id);
                })
                    ->orderBy('name')
                    ->get()->pluck('name')->join(','),
                'episode_used' => Episode::where('user_id', '=', $user->id)->count(),
                'test_used' => Test::where('user_id', '=', $user->id)->count()
            ];
        return response()->json($res);
    }
}
