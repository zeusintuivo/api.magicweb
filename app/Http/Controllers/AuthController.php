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
                'success' => trans('notify.success.login', compact('email')),
            ],
            'user'   => new UserResource($user->login()),
        ], 200);
    }

    public function authCheck(Request $request)
    {
        $email = $request->user()->email;
        return response()->json([
            'notify' => [
                'info' => trans('notify.info.auth-check', compact('email'))
            ],
            'user' => new UserResource($request->user())
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $email = $user->email;
        return response()->json([
            'notify' => [
                'info' => trans('notify.info.logout', compact('email')),
            ],
            'user'   => new UserResource($user->logout()),
        ], 200);
    }

    /**
     * @tested phpunit
     * @param \App\Http\Requests\AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AuthRequest $request)
    {
        $user = new User();
        $email = $request->input('email');
        return response()->json([
            'notify' => [
                'info' => trans('notify.info.register', compact('email')),
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
                'info' => trans('notify.info.resend-verification', compact('email')),
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
                'success' => trans('notify.success.verify-email', compact('email')),
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
                'info' => trans('notify.info.forgot-password', compact('email')),
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
                'success' => trans('notify.success.reset-password', compact('email')),
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
                'warning' => trans('notify.warning.account-delete-request', compact('email')),
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
                'danger' => trans('notify.danger.account-delete-confirm', compact('email')),
            ],
            'user'   => new UserResource($user->accountDeleteConfirm()),
        ], 200);
    }

}
