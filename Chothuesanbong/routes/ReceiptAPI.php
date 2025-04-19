<?php

use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api', 'authen_admin:api'],
    'prefix' => '/receipts'
], function () {
    Route::get('revenue-by-field', [ReceiptController::class, 'revenueByField']);
});


