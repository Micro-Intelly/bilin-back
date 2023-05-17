<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Requests\UpdateCommentRequest;
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
use Mews\Purifier\Facades\Purifier;

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
        return response()->json(
            User::with('organization')->orderBy('email')->get());
    }

    public function show_current_user(Request $request): JsonResponse
    {
        if($request->user() != null){
            $user = User::with('organizations','organization')->findOrFail($request->user()->id);
            return UserController::getUserData($user);
        } else {
            abort(401,'No user is logged');
        }
    }

    public function get_limits(Request $request): JsonResponse
    {
        return response()->json(config('constants.limits'));
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
                ->with('language','organization')
                ->orderBy('updated_at','desc')
                ->get());
    }
    /**
     * Display a listing of the resource.
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
            $request->user()->id === $user->id))
        {
            try {
                $user->delete();

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return response()->json(['status' => 200, 'message' => 'Success']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder|array|null $user
     * @return JsonResponse
     */
    public static function getUserData(User $user): JsonResponse
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
