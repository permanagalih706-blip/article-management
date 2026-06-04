<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [ArticleController::class, 'dashboard'])->name('dashboard');
    Route::get('/drafts', [ArticleController::class, 'drafts'])->name('articles.drafts');
    Route::post('/articles/{article}/publish', [ArticleController::class, 'publish'])->name('articles.publish');
    
    Route::resource('articles', ArticleController::class)->except(['index']);

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

Route::middleware(['auth', 'admin'])->group(function () {

    Route::resource('users', UserController::class);

});

require __DIR__.'/auth.php';