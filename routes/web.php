<?php

use App\Http\Controllers\FrontendController;
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

Route::get('/', [FrontendController::class, 'index']);
Route::get('/user', [FrontendController::class, 'user']);
Route::get('/user/edit', [FrontendController::class, 'userEdit'])->middleware('auth');
Route::get('/album', [FrontendController::class, 'album']);
