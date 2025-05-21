<?php

use App\Http\Controllers\FieldTimeSlotController;
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
    'prefix' => '/field-time-slots'
], function () {
    Route::put('/update-by-date', [FieldTimeSlotController::class, 'updateByDate']);
});
