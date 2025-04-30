<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('google/login', [\App\Http\Controllers\GoogleController::class, 'redirectToGoogle']);
//Route::get('auth/google/callback', [\App\Http\Controllers\GoogleController::class, 'handleGoogleCallback']);

Route::post('auth/google/login', [\App\Http\Controllers\GoogleController::class, 'handleGoogleLogin']);



