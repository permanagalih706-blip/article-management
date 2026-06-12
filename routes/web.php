<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
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

});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::middleware(['auth', 'superadmin'])->group(function () {

    Route::resource('users', UserController::class);

});

Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'showPublic'])->name('articles.show');

require __DIR__.'/auth.php';