<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\CommentReportController;
use App\Http\Controllers\AllowedWordController;
use App\Http\Controllers\ModerationLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArticleController::class, 'index']);

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [ArticleController::class, 'dashboard'])->name('dashboard');
    Route::get('/drafts', [ArticleController::class, 'drafts'])->name('articles.drafts');
    Route::post('/articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    
    // Media routes
    Route::post('/articles/{article}/media/{media}/set-cover', [ArticleController::class, 'setCover'])->name('articles.media.set-cover');
    Route::delete('/media/{media}', [ArticleController::class, 'deleteMedia'])->name('media.destroy');
    Route::post('/articles/{article}/media/reorder', [ArticleController::class, 'reorderMedia'])->name('articles.media.reorder');

    Route::resource('articles', ArticleController::class)->except(['index', 'show']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Comments
    Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Rating (AJAX)
    Route::post('/articles/{article}/rate', [RatingController::class, 'store'])->name('ratings.store');

    // Report komentar
    Route::post('/comments/{comment}/report', [CommentReportController::class, 'store'])->name('comments.report');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::middleware(['auth', 'superadmin'])->group(function () {

    Route::resource('users', UserController::class);

    // Admin: Moderasi
    Route::prefix('admin')->group(function () {
        // Blocklist management
        Route::get('/allowed-words', [AllowedWordController::class, 'index'])->name('admin.allowed-words.index');
        Route::post('/allowed-words', [AllowedWordController::class, 'store'])->name('admin.allowed-words.store');
        Route::delete('/allowed-words/{allowedWord}', [AllowedWordController::class, 'destroy'])->name('admin.allowed-words.destroy');

        // Report management
        Route::get('/reports', [CommentReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/reports/{report}', [CommentReportController::class, 'show'])->name('admin.reports.show');
        Route::put('/reports/{report}/resolve', [CommentReportController::class, 'resolve'])->name('admin.reports.resolve');

        // Moderation logs
        Route::get('/moderation-logs', [ModerationLogController::class, 'index'])->name('admin.moderation-logs.index');
    });
});

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'showPublic'])->name('articles.show');

require __DIR__.'/auth.php';