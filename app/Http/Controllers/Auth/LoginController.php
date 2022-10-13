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
}
