<?php

use App\Http\Controllers\RsvpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RsvpController::class, 'show'])->name('wedding.show');
Route::post('/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');
Route::post('/wishes', [RsvpController::class, 'storeWish'])->name('wishes.store');
Route::get('/api/wishes', [RsvpController::class, 'wishes'])->name('wishes.index');

// Temp debug — xóa sau khi xác nhận hoạt động
Route::get('/debug-env', function () {
    $b64 = getenv('GOOGLE_SHEETS_CREDENTIALS_BASE64');
    $json = getenv('GOOGLE_SHEETS_CREDENTIALS_JSON');
    return response()->json([
        'CREDENTIALS_BASE64_length' => $b64 ? strlen($b64) : 0,
        'CREDENTIALS_BASE64_prefix' => $b64 ? substr($b64, 0, 20) . '...' : null,
        'CREDENTIALS_JSON_length'   => $json ? strlen($json) : 0,
        'SPREADSHEET_ID'            => getenv('GOOGLE_SHEETS_SPREADSHEET_ID'),
        'config_base64_length'      => strlen(config('services.google_sheets.credentials_base64', '')),
        'php_version'               => PHP_VERSION,
    ]);
});

// Admin - Danh sách xác nhận tham dự
Route::get('/admin/rsvp', [RsvpController::class, 'adminRsvp'])->name('admin.rsvp');
Route::get('/admin/sheets-test', [RsvpController::class, 'testGoogleSheets'])->name('admin.sheets.test');
Route::post('/admin/login', [RsvpController::class, 'adminLogin'])->name('admin.login');
Route::post('/admin/logout', [RsvpController::class, 'adminLogout'])->name('admin.logout');
Route::delete('/admin/rsvp/{rsvp}/wish', [RsvpController::class, 'deleteWish'])->name('admin.wish.delete');
Route::delete('/admin/rsvp', [RsvpController::class, 'deleteAllRsvps'])->name('admin.rsvp.deleteAll');
Route::get('/admin/config', [RsvpController::class, 'adminConfig'])->name('admin.config');
Route::post('/admin/config', [RsvpController::class, 'updateConfig'])->name('admin.config.update');
Route::get('/admin/gallery', [RsvpController::class, 'adminGallery'])->name('admin.gallery');
Route::get('/admin/visits', [RsvpController::class, 'adminVisits'])->name('admin.visits');
Route::delete('/admin/visits/{visit}', [RsvpController::class, 'deleteVisit'])->name('admin.visit.delete');
Route::delete('/admin/visits', [RsvpController::class, 'deleteAllVisits'])->name('admin.visits.deleteAll');
Route::post('/admin/gallery', [RsvpController::class, 'storeGalleryPhoto'])->name('admin.gallery.store');
Route::put('/admin/gallery/{photo}', [RsvpController::class, 'updateGalleryPhoto'])->name('admin.gallery.update');
Route::delete('/admin/gallery/{photo}', [RsvpController::class, 'deleteGalleryPhoto'])->name('admin.gallery.delete');
