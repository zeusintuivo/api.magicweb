<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function response;
use function trans;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * Report or log an exception.
     *
     * @param Exception $exception
     *
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     *
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelNotFoundException) {
            return response()->json($e->getModel(), 400);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json(['message' => $e->getMessage(), 'guards' => $e->guards()], 401);
        }

        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => $e->getMessage() . ' [FormRequest]',
                'where'   => "{$e->getFile()}:{$e->getLine()}",
            ], 403);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json($e->getMessage() ?: "Route not found", 404);
        }

        if ($e instanceof MethodNotAllowedHttpException) {
            return response()->json("Change HTTP method. Allowed is {$e->getHeaders()['Allow']}.", 405);
        }

        if ($e instanceof ValidationException) {
            return response()->json($e->errors(), 422);
        }

        if ($e instanceof DecryptException) {
            return response()->json(trans('validation.exception.decrypt'), 422);
        }

        if ($e instanceof TokenNotFoundException) {
            return response()->json(trans('validation.exception.token.not.found'), 422);
        }

        if ($e instanceof TokenExpiredException) {
            return response()->json(trans('validation.exception.token.expired', [
                'minutes' => config('auth.passwords.users.expire')
            ]), 422);
        }

        return response()->json($e->getMessage(), 500);
    }

    protected function unauthenticated($request, AuthenticationException $e)
    {
        return response()->json(trans('auth.unauthenticated'), 401);
    }
}
