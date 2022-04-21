<?php

use Illuminate\Http\Request;
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

Route::get('/version', [\App\Http\Controllers\Api\VersionController::class, 'index']);

Route::post('/login', [\App\Http\Controllers\Api\V1\AuthController::class, 'login'])->name('login');
Route::post('/register', [\App\Http\Controllers\Api\V1\AuthController::class, 'register'])->name('register');
Route::post('/restore', [\App\Http\Controllers\Api\V1\AuthController::class, 'restore'])->name('restore');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\V1\AuthController::class, 'logout'])->name('logout');
});
