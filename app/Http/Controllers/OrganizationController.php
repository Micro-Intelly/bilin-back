<?php

namespace App\Http\Controllers;

use App\Models\Org_user;
use App\Models\Organization;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;

class OrganizationController extends Controller
{
    /**
     * Display organization users.
     *
     * @param Request $request
     * @param Organization $organization
     * @return JsonResponse
     */
    public function index_users(Request $request, Organization $organization): JsonResponse
    {
        if($request->user()->organization_id === $organization->id){
            return response()->json(
                Organization::where('id','=',$organization->id)->with('users')->get());
        } else {
            abort(401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Organization $organization
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function add_user(Request $request, Organization $organization): JsonResponse
    {
        $this->validate($request, [
            'email' => 'required|exists:users,email',
        ], [
            'email.exists' => 'Email not exist'
        ]);
        if($request->user()->organization_id == $organization->id ||
        $request->user()->can('manage-user'))
        {
            $user = User::where('email', '=', $request->get('email'))->get();
            $user = $user[0];
            $checkOrgUser = Org_user::where('user_id', '=', $user->id)
                ->where('organization_id','=',$organization->id)
                ->count();
            if($checkOrgUser < 1){
                try{

                    $user->organizations()->attach($organization->id);
                    return response()->json(['status' => 200, 'message' => 'User added to org']);
                } catch (Exception $exception) {
                    return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
                }
            } else {
                return response()->json(['status' => 400, 'message' => 'User already exists']);
            }
        } else {
            abort(401);
        }
    }

    /**
     * Remove user from organization.
     *
     * @param Request $request
     * @param \App\Models\Organization $organization
     * @param User $user
     * @return JsonResponse
     */
    public function delete_user(Request $request, Organization $organization, User $user): JsonResponse
    {
        if($request->user()->organization_id == $organization->id ||
            $request->user()->can('manage-user'))
        {
            try{
                $user->organizations()->detach($organization->id);
                return response()->json(['status' => 200, 'message' => 'User deleted to org']);
            } catch (Exception $exception) {
                return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
            }
        } else {
            abort(401);
        }
    }
}
