<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Models\Episode;
use App\Models\Org_user;
use App\Models\Organization;
use App\Models\Serie;
use App\Models\Test;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use \Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Method to login user
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $rule = [
            'email' => 'required|email|max:255',
            'password' => 'required|max:255'
        ];

        $validated = $request->validate($rule);

        $credentials = $request->only('email', 'password');
        if( ! Auth::attempt($credentials)){
           throw ValidationException::withMessages([
              'email' => [
                  __('auth.failed')
              ]
           ]);
        }

        $request->session()->regenerate();
        return response()->json($request->user());
    }

    /**
     * Method to logout user
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'User logged out successfully'
        ]);
    }

    /**
     * Method to check if user is logged in
     * @param Request $request
     * @return JsonResponse
     */
    public function isLoggedIn(Request $request): JsonResponse
    {
        $checkAuth = $request->user() != null;
        $res = response()->json(null);
        if($checkAuth){
            $user = User::with('organizations','organization')->findOrFail($request->user()->id);
            $res = UserController::getUserData($user);
        }
        return $res;
    }
}
