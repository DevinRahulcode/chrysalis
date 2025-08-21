<?php

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogDetailImageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//Blog
Route::group(['prefix' => 'admin', 'as' => 'blog.', 'namespace' => 'Admin'], function () {
    Route::get('blogs', [BlogController::class, 'index'])->name('index');
    Route::get('blogs/create', [BlogController::class, 'create'])->name('create');
    Route::post('blogs', [BlogController::class, 'store'])->name('store');
    Route::get('blogs/{id}', [BlogController::class, 'show'])->name('show');
    Route::put('blogs/{id}', [BlogController::class, 'update'])->name('update');
    Route::delete('blogs/{id}', [BlogController::class, 'destroy'])->name('destroy');
    Route::post('blogs/activation', [BlogController::class, 'activation'])->name('activation');
    Route::get('blogs/ajax-data', [BlogController::class, 'getAjaxBlogData'])->name('ajax.data');
});

//Blog Detail Images
Route::group(['prefix' => 'admin', 'as' => 'blog_detail_images.', 'namespace' => 'Admin'], function () {
    Route::get('blog-detail-images', [BlogDetailImageController::class, 'index'])->name('index');
    Route::get('blog-detail-images/create', [BlogDetailImageController::class, 'create'])->name('create');
    Route::post('blog-detail-images', [BlogDetailImageController::class, 'store'])->name('store');
    Route::get('blog-detail-images/{id}', [BlogDetailImageController::class, 'show'])->name('show');
    Route::put('blog-detail-images/{id}', [BlogDetailImageController::class, 'update'])->name('update');
    Route::delete('blog-detail-images/{id}', [BlogDetailImageController::class, 'destroy'])->name('destroy');
    Route::get('blog-detail-images/ajax-data', [BlogDetailImageController::class, 'getAjaxBlogDetailImageData'])->name('ajax.data');
});

require __DIR__.'/auth.php';
