<?php

use App\Http\Controllers\CommentController;
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

Route::group([
    'middleware' => 'auth:api',
    'prefix' => '/comment'],
    function () {
        Route::get('findByFieldId/{field_id}', [\App\Http\Controllers\CommentController::class, 'findByFieldId']);
        Route::get('{comment_id}', [\App\Http\Controllers\CommentController::class, 'findById']);
        Route::post('create', [\App\Http\Controllers\CommentController::class, 'store']);
        Route::post('update/{comment_id}', [\App\Http\Controllers\CommentController::class, 'update']);
        Route::delete('{id}', [\App\Http\Controllers\CommentController::class, 'destroy']); // Xóa comment
    });





