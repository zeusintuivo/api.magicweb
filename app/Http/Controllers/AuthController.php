<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $v = $request->validate([
            'email'    => 'required|email|exists:users',
            'password' => 'required|string|min:6|max:30',
        ]);

        if (!$user = User::whereEmail($v['email'])->first()) {
            throw new Exception("User with email {$v['email']} could not be found.");
        }

        $user->api_token = Str::random(60);
        $user->save();

        return response()->json($user, 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->api_token = null;
        $user->save();
        return response()->json([
            'info' => "User has been successfully signed out.",
            'user' => $user,
        ], 200);
    }

    public function register(Request $request)
    {
        $v = $request->validate([
            'first_name'            => 'required',
            'last_name'             => 'required',
            'email'                 => 'required',
            'password'              => 'required',
            'password_confirmation' => 'required',
        ]);

        $user = User::create([
            'first_name' => $v['first_name'],
            'last_name'  => $v['last_name'],
            'email'      => $v['email'],
            'password'   => hash('sha256', $v['password']),
        ]);

        return response()->json($user, 200);
    }

    public function verifyEmail(Request $request)
    {
        return response()->json($request, 200);
    }

    public function forgotPassword(Request $request)
    {
        return response()->json($request, 200);
    }

    public function resetPassword(Request $request)
    {
        return response()->json($request, 200);
    }
}
