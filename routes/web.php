<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web3AuthController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\LeaderboardUser;
use App\Http\Controllers\Statistic;
use App\Http\Controllers\AspiroBot;

Route::get('/', function () {
    return view('landing');
});

Route::get('/leaderboard', [LeaderboardUser::class, 'index']);
Route::get('/aspiro', [Statistic::class, 'index']);
Route::get('/statistic', [AspiroBot::class, 'index']);

Route::get('/dashboard', [PostController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Web3 / Metamask Authentication
Route::prefix('eth')->group(function () {
    Route::get('/signature', [Web3AuthController::class, 'signature']);
    Route::post('/authenticate', [Web3AuthController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    Route::post('/posts/{post}/toggle-like', [LikeController::class, 'toggleLike'])->name('posts.like');

    Route::get('/posts/{post}', [ReplyController::class, 'show'])->name('posts.show');
    Route::post('/posts/{post}/reply', [ReplyController::class, 'store'])->name('posts.reply');
    Route::get('/posts/{post}/replies', [ReplyController::class, 'replies'])->name('posts.replies');


});

require __DIR__.'/auth.php';
