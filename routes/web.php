<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\KamarAdminController; 
use App\Http\Controllers\AdminBookingViewController;

Route::get('/beranda', [BerandaController::class, 'edit'])->name('beranda.edit');
Route::put('/beranda/{id}', [BerandaController::class, 'update'])->name('beranda.update');

Route::get('/', [BerandaController::class, 'show'])->name('beranda.show');
Route::get('/cek-kamar', [BerandaController::class, 'cekKamar'])->name('cek.kamar');

/*
|--------------------------------------------------------------------------
| BOOKING — INDIVIDU
|--------------------------------------------------------------------------
*/

// Form individu (GET)
Route::get('/booking/individu', [BookingController::class, 'bookingIndividu'])
    ->name('booking.individu');

// Submit form individu (POST)
Route::post('/booking/individu', [BookingController::class, 'storeIndividu'])
    ->name('booking.individu.store');


/*
|--------------------------------------------------------------------------
| BOOKING — CORPORATE
|--------------------------------------------------------------------------
*/

// Form corporate (GET)
Route::get('/booking/corporate', [BookingController::class, 'bookingCorporate'])
    ->name('booking.corporate');

// Submit corporate (POST)
Route::post('/booking/corporate', [BookingController::class, 'storeCorporate'])
    ->name('booking.corporate.store');


/*
|--------------------------------------------------------------------------
| PAYMENT
|--------------------------------------------------------------------------
*/
Route::get('/booking/payment/{id}', [BookingController::class, 'payment'])
    ->name('booking.payment');

Route::post('/booking/payment/{id}/upload', [BookingController::class, 'uploadBuktiPembayaran'])
    ->name('booking.payment.upload');


/*
|--------------------------------------------------------------------------
| ADMIN — BOOKING LIST (BARU)
|--------------------------------------------------------------------------
*/

// Daftar semua booking (corporate + individu)
Route::get('/admin/booking', [AdminBookingController::class, 'index'])
    ->name('admin.booking.index');

// Detail booking
Route::get('/admin/booking/detail/{id}', [AdminBookingController::class, 'show'])
    ->name('admin.booking.detail');

// Hapus booking
Route::delete('/admin/booking/{id}', [AdminBookingController::class, 'destroy'])
    ->name('admin.booking.delete');


// =====================
// ADMIN KAMAR (EDIT HARGA & FOTO)
// =====================
Route::get('/admin/kamar', [KamarAdminController::class, 'index'])
    ->name('admin.kamar.index');

Route::post('/admin/kamar/{id}', [KamarAdminController::class, 'update'])
    ->name('admin.kamar.update');

// ADMIN BOOKING LIST
Route::get('/admin/booking', [\App\Http\Controllers\AdminBookingController::class, 'index'])
    ->name('admin.booking');
