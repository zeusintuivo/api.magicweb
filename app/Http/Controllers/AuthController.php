<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Resources\UserResource;
use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        $user = User::whereEmail($request['email'])->first();
        $email = $user->email;
        return response()->json([
            'notify' => [
                'success' => trans('auth.signed.in', compact('email')),
            ],
            'user'   => new UserResource($user->login()),
        ], 200);
    }

    public function authCheck(Request $request)
    {
        return response()->json([
            'notify' => [],
            'user' => new UserResource($request->user())
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $email = $user->email;
        return response()->json([
            'notify' => [
                'info' => trans('auth.signed.out', compact('email')),
            ],
            'user'   => new UserResource($user->logout()),
        ], 200);
    }

    public function register(AuthRequest $request)
    {
        $user = new User();
        $email = $request->input('email');
        return response()->json([
            'notify' => [
                'info' => trans('messages.email.sent', compact('email')),
            ],
            'user'   => new UserResource($user->register()),
        ], 200);
    }

    public function resendVerification(AuthRequest $request)
    {
        $user = User::whereEmail($request->input('email'))->first();
        $email = $user->email;
        return response()->json([
            'notify' => [
                'info' => trans('auth.mail.verification.sent', compact('email')),
            ],
            'user'   => new UserResource($user->resendVerification()),
        ], 200);
    }

    public function verifyEmail(AuthRequest $request)
    {
        $user = Token::find($request->input('token'))->user;
        $email = $user->email;
        // Verify user, and notify
        return response()->json([
            'notify' => [
                'success' => trans('auth.mail.verification.done', compact('email')),
            ],
            'user'   => new UserResource($user->verifyEmail()),
        ], 200);
    }

    public function forgotPassword(AuthRequest $request)
    {
        $user = User::whereEmail($request->input('email'))->first();
        $email = $user->email;
        return response()->json([
            'notify' => [
                'info' => trans('messages.email.sent', compact('email')),
            ],
            'user'   => new UserResource($user->forgotPassword()),
        ], 200);
    }

    public function resetPassword(AuthRequest $request)
    {
        $user = Token::find($request->input('token'))->user;
        $email = $user->email;
        return response()->json([
            'notify' => [
                'success' => trans('auth.password.changed', compact('email')),
            ],
            'user'   => new UserResource($user->resetPassword()),
        ], 200);
    }

    public function accountDeleteRequest(Request $request)
    {
        $user = $request->user();
        $email = $user->email;
        return response()->json([
            'notify' => [
                'warning' => trans('messages.email.sent', compact('email')),
            ],
            'user'   => new UserResource($user->accountDeleteRequest()),
        ], 200);
    }

    public function accountDeleteConfirm(AuthRequest $request)
    {
        $user = Token::find($request->input('token'))->user;
        $email = $user->email;
        return response()->json([
            'notify' => [
                'danger' => trans('auth.account.deleted', compact('email')),
            ],
            'user'   => new UserResource($user->accountDeleteConfirm()),
        ], 200);
    }

}
