<?php 

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DevotionalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;

Route::get('/', [HomeController::class, 'show'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/**
 * Auth-only routes (create/store/edit/update/destroy)
 */
Route::middleware('auth')->group(function () {
    Route::get('devotionals/create', [DevotionalController::class, 'create'])
        ->name('devotionals.create');

    Route::post('devotionals', [DevotionalController::class, 'store'])
        ->name('devotionals.store');

    Route::get('devotionals/{devotional:slug}/edit', [DevotionalController::class, 'edit'])
        ->name('devotionals.edit');

    Route::put('devotionals/{devotional:slug}', [DevotionalController::class, 'update'])
        ->name('devotionals.update');

    Route::delete('devotionals/{devotional:slug}', [DevotionalController::class, 'destroy'])
        ->name('devotionals.destroy');

    Route::post('devotionals/{devotional:slug}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::delete('devotionals/{devotional:slug}/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * Public routes
 */
Route::get('devotionals', [DevotionalController::class, 'index'])
    ->name('devotionals.index');

Route::get('devotionals/{devotional:slug}', [DevotionalController::class, 'show'])
    ->name('devotionals.show');

require __DIR__.'/auth.php';
