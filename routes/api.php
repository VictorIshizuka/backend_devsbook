<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/ping', function () {
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth:api');
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh')->middleware('auth:api');

Route::post('/user', [AuthController::class, 'create'])->name('auth.create');
Route::put('/user', [UserController::class, 'update'])->name('user.update')->middleware('auth:api');
Route::post('/user/avatar', [UserController::class, 'updateAvatar'])->name('user.updateAvatar')->middleware('auth:api');
Route::post('/user/cover', [UserController::class, 'updateCover'])->name('user.updateCover')->middleware('auth:api');

Route::get('/feed', [FeedController::class, 'read'])->name('feed.read');
Route::get('/user/feed', [FeedController::class, 'userFeed'])->name('feed.userFeed');
Route::get('/user/{id}/feed', [FeedController::class, 'userFeed'])->name('feed.userFeed');

Route::get('/user', [UserController::class, 'read'])->name('user.read');
Route::get('/user/{id}', [UserController::class, 'read'])->name('user.read');

Route::post('/feed', [FeedController::class, 'create'])->name('feed.create')->middleware('auth:api');

// Route::post('/post/{id}/like', [PostController, 'like'])->name('post.like');
// Route::post('/post/{id}/comment', [PostController, 'comment'])->name('post.comment');

// Route::get('/search', [SearchController, 'search'])->name('search');
