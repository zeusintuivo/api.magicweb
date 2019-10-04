<?php

namespace App\Http\Controllers;

use App\Exceptions\TokenNotFoundException;
use App\Http\Requests\AuthRequest;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        $user = User::whereEmail($request['email'])->first();
        return response()->json([
            'notify' => [
                'success' => trans('auth.signed.in', ['email' => $user->email]),
            ],
            'user'   => $user->login($request),
        ], 200);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'notify' => [
                'info' => trans('auth.signed.out'),
            ],
            'user'   => $request->user()->logout,
        ], 200);
    }

    public function register(AuthRequest $request)
    {
        $user = new User();
        return response()->json([
            'notify' => [
                'info' => trans('messages.email.sent', ['email' => $request->input('email')]),
            ],
            'user'   => $user->register($request),
        ], 200);
    }

    public function verifyEmail(AuthRequest $request)
    {
        // Find token with hash as ID, and get associated user
        if (!$token = Token::find($request->input('token'))) {
            throw new TokenNotFoundException();
        }
        // Verify user, and notify
        return response()->json([
            'notify' => [
                'success' => trans('auth.verified.on'),
            ],
            'user'   => $token->user->verifyEmail(),
        ], 200);
    }

    public function forgotPassword(AuthRequest $request)
    {
        $user = User::whereEmail($request->input('email'))->first();
        return response()->json([
            'notify' => [
                'info' => trans('messages.email.sent', ['email' => $user->email]),
            ],
            'user'   => $user->forgotPassword($request),
        ], 200);
    }

    public function resetPassword(AuthRequest $request)
    {
        if (!$token = Token::find($request->input('token'))) {
            throw new TokenNotFoundException();
        }
        return response()->json([
            'notify' => [
                'success' => trans('auth.password.changed'),
            ],
            'user'   => $token->user->resetPassword($request->input('password')),
        ], 200);
    }
}
