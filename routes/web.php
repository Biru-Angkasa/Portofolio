<?php

use App\Http\Controllers\Admin\ProfilePhotoController;
use App\Http\Controllers\Admin\WorkItemController;
use App\Http\Controllers\Admin\WorkMediaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\WorkController;
use Illuminate\Support\Facades\Route;

Route::get('/', PortfolioController::class)->name('home');
Route::get('/bukti/{workItem}', WorkController::class)->name('work.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [WorkItemController::class, 'index'])->name('works.index');
    Route::get('/bukti/baru', [WorkItemController::class, 'create'])->name('works.create');
    Route::post('/bukti', [WorkItemController::class, 'store'])->name('works.store');
    Route::get('/bukti/{workItem}/edit', [WorkItemController::class, 'edit'])->name('works.edit');
    Route::put('/bukti/{workItem}', [WorkItemController::class, 'update'])->name('works.update');
    Route::delete('/bukti/{workItem}', [WorkItemController::class, 'destroy'])->name('works.destroy');
    Route::post('/bukti/{workItem}/urutan/{direction}', [WorkItemController::class, 'move'])->name('works.move');
    Route::post('/foto', [ProfilePhotoController::class, 'update'])->name('profile.update');
    Route::delete('/foto', [ProfilePhotoController::class, 'destroy'])->name('profile.destroy');
    Route::post('/bukti/{workItem}/media', [WorkMediaController::class, 'store'])->name('media.store');
    Route::delete('/bukti/{workItem}/media/{workMedia}', [WorkMediaController::class, 'destroy'])->name('media.destroy');
});
