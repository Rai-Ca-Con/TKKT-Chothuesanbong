<?php

use App\Http\Controllers\FieldController;
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
    'middleware' => ['auth:api', 'authen_admin:api'],
    'prefix' => '/fields'
    ], function () {
        Route::post('/', [FieldController::class, 'store']);        // Tạo sân mới
        Route::put('{id}', [FieldController::class, 'update']);     // Cập nhật sân
        Route::delete('{id}', [FieldController::class, 'destroy']); // Xóa sân
    });

Route::group(['prefix' => '/fields'],
    function () {
        Route::post('/filter', [FieldController::class, 'getFilteredFields']); // Lấy tất cả sân theo bộ lọc
        Route::get('/', [FieldController::class, 'index']);        // Lấy tất cả sân
        Route::get('/search', [FieldController::class, 'searchByName']); // Lấy sân theo tên (chat option)
        Route::get('{id}', [FieldController::class, 'show']);      // Lấy chi tiết sân theo ID
    });



