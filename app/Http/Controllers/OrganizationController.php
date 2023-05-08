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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrganizationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrganizationRequest $request)
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrganizationRequest  $request
     * @return \Illuminate\Http\Response
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
     * Display the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrganizationRequest  $request
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organization  $organization
     * @return \Illuminate\Http\Response
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
