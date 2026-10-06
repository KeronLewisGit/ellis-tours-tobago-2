<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TourController;
use Illuminate\Support\Facades\Route;

/*
| Public site.
*/
Route::get('/', [PageController::class, 'home'])->name('home');

Route::controller(PageController::class)->group(function () {
    Route::get('/book', 'book')->name('book');
    Route::get('/about', 'about')->name('about');
    Route::get('/gallery', 'gallery')->name('gallery');
    Route::get('/faq', 'faq')->name('faq');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/search', 'search')->name('search');
});

Route::get('/tours', [TourController::class, 'index'])->name('tours');
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');

Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
Route::post('/reviews', [ReviewController::class, 'store'])->middleware('throttle:6,1')->name('reviews.store');

Route::post('/enquiries/{type}', [EnquiryController::class, 'store'])
    ->middleware('throttle:12,1')
    ->name('enquiries.store');

/*
| Addresses from the previous WordPress site.
*/
foreach (config('site.redirects') as $from => $to) {
    Route::permanentRedirect('/'.$from, $to);
}

/*
| Staff admin.
*/
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AdminController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');

Route::middleware('auth')->prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/enquiries', 'enquiries')->name('enquiries');
    Route::get('/enquiries/{enquiry}', 'enquiry')->name('enquiry');
    Route::patch('/enquiries/{enquiry}', 'updateEnquiry')->name('enquiry.update');
    Route::delete('/enquiries/{enquiry}', 'destroyEnquiry')->name('enquiry.destroy');
    Route::get('/tours', 'tours')->name('tours');
    Route::get('/tours/new', 'createTour')->name('tours.create');
    Route::post('/tours', 'storeTour')->name('tours.store');
    Route::get('/tours/{tour}', 'editTour')->name('tours.edit');
    Route::put('/tours/{tour}', 'updateTour')->name('tours.update');
    Route::delete('/tours/{tour}', 'destroyTour')->name('tours.destroy');
    Route::get('/calendar', 'dates')->name('dates');
    Route::post('/calendar', 'storeDate')->name('dates.store');
    Route::delete('/calendar/{date}', 'destroyDate')->name('dates.destroy');
    Route::get('/reviews', 'reviews')->name('reviews');
    Route::post('/reviews', 'storeReview')->name('reviews.store');
    Route::patch('/reviews/{review}', 'toggleReview')->name('reviews.toggle');
    Route::delete('/reviews/{review}', 'destroyReview')->name('reviews.destroy');
    Route::get('/announcements', 'announcements')->name('announcements');
    Route::post('/announcements', 'storeAnnouncement')->name('announcements.store');
    Route::patch('/announcements/{announcement}', 'toggleAnnouncement')->name('announcements.toggle');
    Route::delete('/announcements/{announcement}', 'destroyAnnouncement')->name('announcements.destroy');
});
