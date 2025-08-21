<?php

use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogDetailImageController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventDetailImageController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\NewsDetailImageController;
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


//News
Route::group(['prefix' => 'admin', 'as' => 'news.', 'namespace' => 'Admin'], function () {
    Route::get('news', [NewsController::class, 'index'])->name('index');
    Route::get('news/create', [NewsController::class, 'create'])->name('create');
    Route::post('news', [NewsController::class, 'store'])->name('store');
    Route::get('news/{id}', [NewsController::class, 'show'])->name('show');
    Route::put('news/{id}', [NewsController::class, 'update'])->name('update');
    Route::delete('news/{id}', [NewsController::class, 'destroy'])->name('destroy');
    Route::post('news/activation', [NewsController::class, 'activation'])->name('activation');
    Route::get('news/ajax-data', [NewsController::class, 'getAjaxNewsData'])->name('ajax.data');
});

Route::group(['prefix' => 'admin', 'as' => 'news_detail_images.', 'namespace' => 'Admin'], function () {
    Route::get('news-detail-images', [NewsDetailImageController::class, 'index'])->name('index');
    Route::get('news-detail-images/create', [NewsDetailImageController::class, 'create'])->name('create');
    Route::post('news-detail-images', [NewsDetailImageController::class, 'store'])->name('store');
    Route::get('news-detail-images/{id}', [NewsDetailImageController::class, 'show'])->name('show');
    Route::put('news-detail-images/{id}', [NewsDetailImageController::class, 'update'])->name('update');
    Route::delete('news-detail-images/{id}', [NewsDetailImageController::class, 'destroy'])->name('destroy');
    Route::get('news-detail-images/ajax-data', [NewsDetailImageController::class, 'getAjaxNewsDetailImageData'])->name('ajax.data');
});

//Events
Route::group(['prefix' => 'admin', 'as' => 'events.', 'namespace' => 'Admin'], function () {
    Route::get('events', [EventController::class, 'index'])->name('index');
    Route::get('events/create', [EventController::class, 'create'])->name('create');
    Route::post('events', [EventController::class, 'store'])->name('store');
    Route::get('events/{id}', [EventController::class, 'show'])->name('show');
    Route::put('events/{id}', [EventController::class, 'update'])->name('update');
    Route::delete('events/{id}', [EventController::class, 'destroy'])->name('destroy');
    Route::post('events/activation', [EventController::class, 'activation'])->name('activation');
    Route::get('events/ajax-data', [EventController::class, 'getAjaxEventData'])->name('ajax.data');
});


Route::group(['prefix' => 'admin', 'as' => 'event_detail_images.', 'namespace' => 'Admin'], function () {
    Route::get('event-detail-images', [EventDetailImageController::class, 'index'])->name('index');
    Route::get('event-detail-images/create', [EventDetailImageController::class, 'create'])->name('create');
    Route::post('event-detail-images', [EventDetailImageController::class, 'store'])->name('store');
    Route::get('event-detail-images/{id}', [EventDetailImageController::class, 'show'])->name('show');
    Route::put('event-detail-images/{id}', [EventDetailImageController::class, 'update'])->name('update');
    Route::delete('event-detail-images/{id}', [EventDetailImageController::class, 'destroy'])->name('destroy');
    Route::get('event-detail-images/ajax-data', [EventDetailImageController::class, 'getAjaxEventDetailImageData'])->name('ajax.data');
});

require __DIR__.'/auth.php';
