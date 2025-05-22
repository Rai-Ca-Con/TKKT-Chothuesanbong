<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => '/bookings'
], function () {
    Route::post('/', [BookingController::class, 'store']); // Đặt sân
    Route::delete('{id}', [BookingController::class, 'cancel']); // Huỷ đặt sân
    Route::get('user', [BookingController::class, 'userBookings']); // Lịch sử đặt sân
    Route::get('/user/today', [BookingController::class, 'userBookingsToday']); // Lịch sử đặt sân trong ngày (chat option)
});

Route::group([
    'middleware' => ['auth:api', 'authen_admin:api'],
    'prefix' => '/bookings'
], function () {
    Route::get('/detail', [BookingController::class, 'getBookingWithReceipt']);
});

Route::get('/vnpay/callback', [BookingController::class, 'handleBookingPayment']);
Route::get('/booked-time-slots/{fieldId}', [BookingController::class, 'getBookedTimeSlots']);
Route::get('/bookings/weekly', [BookingController::class, 'getWeeklyBookings']);
Route::get('/weekly-pricing/{field_id}', [BookingController::class, 'getWeeklyPricing']);


//Route::match(['get', 'post'], '/vnpay/callback', [BookingController::class, 'handleBookingPayment']);
//Route::get('/statistics', [BookingController::class, 'statsUntilDate']);
//Route::get('/statistics/active-users', [BookingController::class, 'mostActiveUsers']);


