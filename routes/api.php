<?php

use App\Http\Controllers\UserController;
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

Route::post('/registration', [UserController::class, 'registration']);
Route::post('/login', [UserController::class, 'login']);
Route::any('/user/check', [UserController::class, 'checkAuth']);
Route::any('/user/info', [UserController::class, 'getInfo'])->middleware('auth');
Route::post('/user/update', [UserController::class, 'update'])->middleware('auth');
