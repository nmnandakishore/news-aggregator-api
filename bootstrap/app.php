<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\InvalidParameterException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //

        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => $exception->getMessage()
            ], 401);
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => $exception->getMessage()
            ], 403);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            return response()->json([
                'error' => 'Not Found',
                'message' => $exception->getMessage()
            ], 404);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $exception, Request $request) {
            return response()->json([
                'error' => 'Not Allowed',
                'message' => $exception->getMessage()
            ], 405);
        });

        $exceptions->render(function (BadMethodCallException $exception, Request $request) {
            return response()->json([
                'error' => 'Not Allowed',
                'message' => $exception->getMessage()
            ], 405);
        });

        $exceptions->render(function (BadFunctionCallException $exception, Request $request) {
            return response()->json([
                'error' => 'Not Allowed',
                'message' => $exception->getMessage()
            ], 405);
        });

        $exceptions->render(function (ValidationException $exception, Request $request) {
            return response()->json([
                'error' => 'Unprocessable',
                'message' => $exception->errors()
            ], 422);
        });

        $exceptions->render(function (InvalidParameterException $exception, Request $request) {
            return response()->json([
                'error' => 'Unprocessable',
                'message' => $exception->getMessage()
            ], 422);
        });

        $exceptions->render(function (ThrottleRequestsException $exception, Request $request) {
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => $exception->getMessage()
            ], 429);
        });

        $exceptions->render(function (QueryException $exception, Request $request) {
            return response()->json([
                'error' => 'Database Error',
                'message' => $exception->getMessage()
            ], 500);
        });

        $exceptions->render(function (Exception $exception, Request $request) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $exception->getMessage()
            ], 500);
        });

    })->create();
