<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LikeController;
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

Route::post('/registration', [UserController::class, 'registration'])->middleware('auth:guest');
Route::post('/login', [UserController::class, 'login'])->middleware('auth:guest');
Route::any('/user/check', [UserController::class, 'checkAuth']);
Route::any('/user/info', [UserController::class, 'getInfo'])->middleware('auth');
Route::post('/user/update', [UserController::class, 'update'])->middleware('auth');
Route::post('/user/follower/add', [FollowerController::class, 'follow'])->middleware('auth');
Route::post('/user/follower/delete', [FollowerController::class, 'unfollow'])->middleware('auth');
Route::any('/user/followers', [FollowerController::class, 'index'])->middleware('auth');


Route::post('/image/add', [ImageController::class, 'add'])->middleware('auth');
Route::any('/image/show', [ImageController::class, 'show']);
Route::post('/image/save', [ImageController::class, 'save'])->middleware('auth');
Route::any('/images', [ImageController::class, 'index']);


Route::post('/album/add', [AlbumController::class, 'add'])->middleware('auth');
Route::any('/album/show', [AlbumController::class, 'show']);
Route::any('/albums', [AlbumController::class, 'index']);

Route::post('/image/comment/add', [CommentController::class, 'add'])->middleware('auth');
Route::any('/image/comments', [CommentController::class, 'index']);

Route::post('/image/like', [LikeController::class, 'like'])->middleware('auth');
Route::post('/image/dislike', [LikeController::class, 'dislike'])->middleware('auth');
Route::any('/likes', [LikeController::class, 'index'])->middleware('auth');
