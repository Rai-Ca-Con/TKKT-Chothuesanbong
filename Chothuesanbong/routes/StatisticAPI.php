<?php

use App\Http\Controllers\StatisticsController;
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
    'prefix' => '/statistics'
], function () {
    Route::get('/revenue-by-field', [StatisticsController::class, 'revenueByField']);        // Tạo sân mới
    Route::get('/revenue-until-date', [StatisticsController::class, 'statsUntilDate']);     // Cập nhật sân
    Route::get('/top-users', [StatisticsController::class, 'mostActiveUsers']); // Xóa sân
});
