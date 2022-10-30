<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(Role::select(['id','name','need_key'])->whereNot('name','Admin')->get());
    }
}
