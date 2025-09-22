<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web3AuthController;

Route::get('/', function () {
    return view('landing');
});

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

});

require __DIR__.'/auth.php';
