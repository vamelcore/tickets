<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login'])
    ->name('login');
Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register'])
    ->name('register');
Route::post('/password-restore', [\App\Http\Controllers\Api\V1\AuthController::class, 'passwordRestore'])
    ->name('password.restore');

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout'])
        ->name('logout');
    Route::post('/email-verification', [\App\Http\Controllers\Api\V1\AuthController::class, 'emailVerification'])
        ->name('verification.send');
    Route::get('/email-verified', [\App\Http\Controllers\Api\V1\AuthController::class, 'emailVerified'])
        ->name('verification.status');
});

Route::middleware(['auth:sanctum','verified'])->group(function () {
    Route::put('/password-update', [\App\Http\Controllers\Api\V1\AuthController::class, 'passwordUpdate'])
        ->name('password.update');
});
