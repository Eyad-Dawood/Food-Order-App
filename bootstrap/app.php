<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use App\Exceptions\BusinessException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
        ]);
        $middleware->api(prepend: [
            \App\Http\Middleware\SetAppLocale::class,
        ]); 
    })->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e) =>
                $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(function (BusinessException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => __($e->getMessage()), 
                    'errors'  => [__($e->getMessage())],
                ], $e->getStatusCode());
            }
        });

        $exceptions->render(function (TokenExpiredException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => __('auth.unauthenticated'),
                    'errors'  => [__('auth.token_expired')],
                ], 401);
            }
        });

        $exceptions->render(function (TokenInvalidException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => __('auth.unauthenticated'),
                    'errors'  => [__('auth.token_invalid')],
                ], 401);
            }
        });

        $exceptions->render(function (JWTException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => __('auth.unauthenticated'),
                    'errors'  => [__('auth.token_error')],
                ], 401);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => __('auth.unauthenticated'),
                    'errors'  => [__('auth.unauthenticated')],
                ], 401);
            }
        });

    })->create();
