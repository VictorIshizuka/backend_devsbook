<?php


use App\Http\Controllers\AuthController;
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
Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('auth.refresh')->middleware('auth:api');;

Route::post('/user', [AuthController::class, 'create'])->name('auth.create');
// Route::put('/user', [UserController, 'update'])->name('user.update');
// Route::post('/user/avatar', [UserController, 'updateAvatar'])->name('user.updateAvatar');
// Route::post('/user/cover', [UserController, 'updateCover'])->name('user.updateCover');

// Route::get('/feed', [FeedController, 'read'])->name('feed.read');
// Route::get('/user/feed', [FeedController, 'userFeed'])->name('feed.userFeed');
// Route::get('/user/{id}/feed', [FeedController, 'userFeed'])->name('feed.userFeed');

// Route::get('/user', [UserController, 'read'])->name('user.read');
// Route::get('/user/{id}', [UserController, 'read'])->name('user.read');

// Route::post('/feed', [FeedController, 'create'])->name('feed.create');

// Route::post('/post/{id}/like', [PostController, 'like'])->name('post.like');
// Route::post('/post/{id}/comment', [PostController, 'comment'])->name('post.comment');

// Route::get('/search', [SearchController, 'search'])->name('search');