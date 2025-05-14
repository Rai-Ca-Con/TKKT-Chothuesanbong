<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(
    ['middleware' => 'auth:api',
        'prefix' => '/user'],
    function () {
        Route::get('getAllUser', [\App\Http\Controllers\UserController::class, 'getAllUser'])->middleware('authen_admin:api');
        Route::get('getUserByKeyword', [\App\Http\Controllers\UserController::class, 'getUserByKeyword'])->middleware('authen_admin:api');
        Route::post('update/{user_id}', [\App\Http\Controllers\UserController::class, 'update']);
        Route::delete('delete/{user_id}', [\App\Http\Controllers\UserController::class, 'destroy']);
    });

Route::post('user/create', [\App\Http\Controllers\UserController::class, 'store']);






