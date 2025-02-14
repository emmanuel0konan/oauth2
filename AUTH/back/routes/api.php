<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('test', function () {
    return response()->json(['message' => 'Hello world']);
});
//Route::get('/redirectToGoogle' ,[AuthController::class,'redirectToGoogle'])->name('redirectToGoogle');
Route::post('/login', [AuthController::class,'login']);
Route::post('/register', [AuthController::class, 'register']);