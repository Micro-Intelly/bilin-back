<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'email' => 'required|email|max:50',
            'password' => 'required|max:30',
            'role' => 'required|max:125|exists:roles,name',
            'verificationKey' => 'max:50'
        ];
        // validate key
//        $validator = Validator::make($request->all(),$rule);

        $validated = $request->validate($rule);

        $credentials = $request->only('username','email', 'password', 'role', 'key');

        $user = new User([
            'name' => $credentials['username'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);
        $user->save();
        $user->assignRole($credentials['role']);

        return response()->json(['message' => 'User '. $user->email .' created'], Response::HTTP_CREATED);
    }
}
