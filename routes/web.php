<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DevotionalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\UserReadController;

Route::get('/', [HomeController::class, 'show'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/**
 * Auth-only routes (create/store/edit/update/destroy)
 */
Route::middleware('auth')->group(function () {
    // Devotionals (auth)
    Route::get('devotionals/create', [DevotionalController::class, 'create'])
        ->name('devotionals.create');

    Route::post('devotionals', [DevotionalController::class, 'store'])
        ->name('devotionals.store');

    Route::get('devotionals/{devotional:slug}/edit', [DevotionalController::class, 'edit'])
        ->name('devotionals.edit');

    Route::patch('devotionals/{devotional:slug}', [DevotionalController::class, 'update'])
        ->name('devotionals.update');

    Route::delete('devotionals/{devotional:slug}', [DevotionalController::class, 'destroy'])
        ->name('devotionals.destroy');

    // Comments (auth)
    Route::post('devotionals/{devotional:slug}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::delete('devotionals/{devotional:slug}/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    // Profiles (auth self)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Convenience: go to my own public profile
    Route::get('/profile/me', [ProfileController::class, 'me'])->name('profile.me');

    // Follow / Unfollow
    Route::post('/u/{user}/follow',  [FollowController::class, 'store'])->name('follow.store');
    Route::delete('/u/{user}/follow', [FollowController::class, 'destroy'])->name('follow.destroy');

    Route::get('/u/{user}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
    Route::get('/u/{user}/following', [ProfileController::class, 'following'])->name('profile.following');
});

/**
 * Public routes
 */
Route::get('devotionals', [DevotionalController::class, 'index'])
    ->name('devotionals.index');

Route::get('devotionals/{devotional:slug}', [DevotionalController::class, 'show'])
    ->name('devotionals.show');

// Public profile by user id (MVP)
Route::get('/u/{user}', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/devotionals/{devotional}/read', [UserReadController::class, 'store'])
    ->middleware('auth')
    ->name('devotionals.read');

require __DIR__.'/auth.php';
