<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * The list of the exception types that are not reported.
     *
     * @var array<int, string>
     */
    protected $dontReport = [
        \App\Exceptions\AppException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // handle token khong hop le
    public function unauthenticated($request, AuthenticationException $exception)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json([
                'message' => 'Token đã hết hạn',
                'code' => '1000'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'message' => 'Token không hợp lệ',
                'code' => '1001'
            ], 401);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Token không được cung cấp hoặc không xác định',
                'code' => '1002'
            ], 401);
        }
    }
}
