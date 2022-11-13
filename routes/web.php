<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\IndexController::class, 'index'])
    ->name('home');
Route::get('/email-verify/{id}/{hash}', [\App\Http\Controllers\EmailVerificationController::class, 'index'])
    ->middleware(['signed'])
    ->name('verification.verify');

//Route::any('{any}', [\App\Http\Controllers\IndexController::class, 'index']);
