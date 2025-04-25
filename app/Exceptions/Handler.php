<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (AuthenticationException $e) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        });

        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'message' => 'Resource not found'
            ], 404);
        });

        $this->renderable(function (Throwable $e) {
            if (!app()->environment('local')) {
                return response()->json([
                    'message' => 'Internal server error'
                ], 500);
            }
        });
    }
}
