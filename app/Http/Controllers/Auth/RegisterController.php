<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    /**
     * User sign up method
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request): JsonResponse
    {
        $rule = [
            'username' => 'required|max:50',
            'email' => 'required|email|max:100',
            'password' => 'required|max:30',
            'role' => 'required|max:125|exists:roles,name',
            'verificationKey' => 'max:50|nullable'
        ];
        // validate key
//        $validator = Validator::make($request->all(),$rule);

        $validated = $request->validate($rule);

        $credentials = $request->only('username','email', 'password', 'role', 'key', 'orgName', 'orgDescription');
        if(User::where('email','=',$credentials['email'])->count() > 0){
            return response()->json(['status' => 400, 'message' => 'User with email '. $credentials['email'] .' already exists']);
        }
        if(Organization::where('name','=',$credentials['orgName'])->count() > 0){
            return response()->json(['status' => 400, 'message' => 'Organization with name '. $credentials['orgName'] .' already exists']);
        }

        try{
            $organization = null;
            if($credentials['role'] == 'Organization'){
                $organization = new Organization([
                    'name' =>  $credentials['orgName'],
                    'description' =>  $credentials['orgDescription'],
                ]);
                $organization->save();
            }
            $user = new User([
                'name' => $credentials['username'],
                'email' => $credentials['email'],
                'password' => Hash::make($credentials['password']),
                'organization_id' => $organization?->id
            ]);
            $user->save();
            $user->assignRole($credentials['role']);

            return response()->json(['status' => 200, 'message' => 'User '. $user->email .' created']);
        } catch (Exception $exception) {
            return response()->json(['status' => 400, 'message' => $exception->getMessage()]);
        }
    }
}
