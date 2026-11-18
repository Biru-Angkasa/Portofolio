<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\CvController;
use App\Http\Controllers\Admin\ProfilePhotoController;
use App\Http\Controllers\Admin\WorkItemController;
use App\Http\Controllers\Admin\WorkMediaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CvDownloadController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WorkController;
use Illuminate\Support\Facades\Route;

Route::get('/', PortfolioController::class)->name('home');
Route::permanentRedirect('/beranda', '/')->name('beranda');
Route::get('/sitemap.xml', SitemapController::class)->withoutMiddleware('web')->name('sitemap');
Route::get('/cv/unduh', CvDownloadController::class)->name('cv.download');
Route::permanentRedirect('/bukti/4', '/');
Route::get('/bukti/{workItem}', WorkController::class)->name('work.show');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{blogPost}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/akun', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/akun', [AccountController::class, 'update'])->middleware('throttle:5,1')->name('account.update');
    Route::put('/cv', [CvController::class, 'update'])->name('cv.update');
    Route::delete('/cv', [CvController::class, 'destroy'])->name('cv.destroy');
    Route::get('/konten', [ContentController::class, 'index'])->name('content.index');
    Route::get('/konten/{section}', [ContentController::class, 'edit'])->name('content.edit')->whereIn('section', array_keys(config('portfolio-editor')));
    Route::put('/konten/{section}', [ContentController::class, 'update'])->name('content.update')->whereIn('section', array_keys(config('portfolio-editor')));
    Route::get('/blog', [BlogPostController::class, 'index'])->name('blog.index');
    Route::get('/blog/baru', [BlogPostController::class, 'create'])->name('blog.create');
    Route::post('/blog', [BlogPostController::class, 'store'])->name('blog.store');
    Route::get('/blog/{blogPost}/edit', [BlogPostController::class, 'edit'])->name('blog.edit');
    Route::put('/blog/{blogPost}', [BlogPostController::class, 'update'])->name('blog.update');
    Route::delete('/blog/{blogPost}', [BlogPostController::class, 'destroy'])->name('blog.destroy');
    Route::get('/', [WorkItemController::class, 'index'])->name('works.index');
    Route::get('/bukti/baru', [WorkItemController::class, 'create'])->name('works.create');
    Route::post('/bukti', [WorkItemController::class, 'store'])->name('works.store');
    Route::get('/bukti/{workItem}/edit', [WorkItemController::class, 'edit'])->name('works.edit');
    Route::put('/bukti/{workItem}', [WorkItemController::class, 'update'])->name('works.update');
    Route::delete('/bukti/{workItem}', [WorkItemController::class, 'destroy'])->name('works.destroy');
    Route::post('/bukti/{workItem}/urutan/{direction}', [WorkItemController::class, 'move'])->name('works.move');
    Route::post('/foto', [ProfilePhotoController::class, 'update'])->name('profile.update');
    Route::get('/foto/asli', [ProfilePhotoController::class, 'original'])->name('profile.original');
    Route::delete('/foto', [ProfilePhotoController::class, 'destroy'])->name('profile.destroy');
    Route::post('/bukti/{workItem}/media', [WorkMediaController::class, 'store'])->name('media.store');
    Route::delete('/bukti/{workItem}/media/{workMedia}', [WorkMediaController::class, 'destroy'])->name('media.destroy');
});
