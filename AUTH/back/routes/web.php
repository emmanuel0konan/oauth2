<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
})->name("welcome");
Route::get('/login', function () {
    return view('login');
})->name("welcome");
Route::get('/redirect', function () {
    return view('redirect');
})->name("redirect");
Route::get('/redirectToGoogle' ,[AuthController::class,'redirectToGoogle'])->name('redirectToGoogle');

Route::get('auth/google/callback',[AuthController::class,'handleGoogleCallback'] )->name('user-google.callback');
