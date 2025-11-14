<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DevotionalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\UserReadController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlanProgressController;

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

    // Added Reading Plans (auth actions)
    Route::post('plans/{plan:slug}/start', [PlanController::class, 'start'])->name('plans.start');
    Route::post('plans/{plan:slug}/pause', [PlanController::class, 'pause'])->name('plans.pause');
    Route::post('plan-entries/{entry}/toggle', [PlanProgressController::class, 'toggle'])->name('plan_entries.toggle');
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

// Mark today's devotional as read (auth)
Route::post('devotionals/{devotional:slug}/read', [UserReadController::class, 'store'])
    ->middleware(['auth'])
    ->name('devotionals.read');

// Added Reading Plans (public views)
Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
Route::get('plans/{plan:slug}', [PlanController::class, 'show'])->name('plans.show');
//Global Search
Route::get('/search', SearchController::class)->name('search.index');


require __DIR__.'/auth.php';
