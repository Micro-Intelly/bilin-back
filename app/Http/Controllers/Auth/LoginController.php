<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
}
