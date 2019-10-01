<?php

namespace App\Http\Controllers\Izgrev;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function loginUser(Request $request)
    {
        return response()->json($request, 200);
    }

    public function registerUser(Request $request)
    {
        return response()->json($request, 200);
    }

    public function forgotPasswordUser(Request $request)
    {
        return response()->json($request, 200);
    }

    public function resetPasswordUser(Request $request)
    {
        return response()->json($request, 200);
    }

    public function verifyEmailUser(Request $request)
    {
        return response()->json($request, 200);
    }

    public function logoutUser(Request $request)
    {
        return response()->json($request, 200);
    }
}
