<?php

use App\Http\Controllers\RsvpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RsvpController::class, 'show'])->name('wedding.show');
Route::post('/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');
Route::get('/api/wishes', [RsvpController::class, 'wishes'])->name('wishes.index');

// Admin - Danh sách xác nhận tham dự
Route::get('/admin/rsvp', [RsvpController::class, 'adminRsvp'])->name('admin.rsvp');
Route::post('/admin/login', [RsvpController::class, 'adminLogin'])->name('admin.login');
Route::post('/admin/logout', [RsvpController::class, 'adminLogout'])->name('admin.logout');
Route::delete('/admin/rsvp/{rsvp}/wish', [RsvpController::class, 'deleteWish'])->name('admin.wish.delete');
Route::get('/admin/config', [RsvpController::class, 'adminConfig'])->name('admin.config');
Route::post('/admin/config', [RsvpController::class, 'updateConfig'])->name('admin.config.update');
Route::get('/admin/gallery', [RsvpController::class, 'adminGallery'])->name('admin.gallery');
Route::post('/admin/gallery', [RsvpController::class, 'storeGalleryPhoto'])->name('admin.gallery.store');
Route::put('/admin/gallery/{photo}', [RsvpController::class, 'updateGalleryPhoto'])->name('admin.gallery.update');
Route::delete('/admin/gallery/{photo}', [RsvpController::class, 'deleteGalleryPhoto'])->name('admin.gallery.delete');
