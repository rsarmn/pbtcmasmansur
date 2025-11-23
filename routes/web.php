<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PengunjungController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\KamarAdminController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES - Landing Page & User Area
|--------------------------------------------------------------------------
*/

// Landing Page / Beranda
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
| PAYMENT (Public)
|--------------------------------------------------------------------------
*/
Route::get('/booking/payment/{id}', [BookingController::class, 'payment'])
    ->name('booking.payment');

Route::post('/booking/payment/{id}/upload', [BookingController::class, 'uploadBuktiPembayaran'])
    ->name('booking.payment.upload');

Route::get('/booking/success/{id}', [BookingController::class, 'success'])
    ->name('booking.success');

// Public user status page (for testing only)
Route::get('/user/test', function(){
    $totalKamar = \App\Models\Kamar::count();
    $kamarKosong = \App\Models\Kamar::where('status','kosong')->count();
    $jumlahPengunjung = \App\Models\Pengunjung::count();
    $pendingPayments = \App\Models\Pengunjung::where('payment_status','pending')->count();
    return view('user.status', compact('totalKamar','kamarKosong','jumlahPengunjung','pendingPayments'));
})->name('user.test');

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

// AUTH
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware([\App\Http\Middleware\AdminAuth::class])->group(function () {

    // Dashboard Admin
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Beranda Management (Admin only)
    Route::get('/beranda', [BerandaController::class, 'edit'])->name('beranda.edit');
    Route::put('/beranda/{id}', [BerandaController::class, 'update'])->name('beranda.update');

    /*
    |--------------------------------------------------------------------------
    | KAMAR Management
    |--------------------------------------------------------------------------
    */
    // Primary route using KamarController
    Route::get('/admin/kamar', [KamarController::class, 'index'])->name('kamar.index');
    Route::get('/admin/kamar/tambah', [KamarController::class, 'create'])->name('kamar.create');
    Route::post('/admin/kamar/simpan', [KamarController::class, 'store'])->name('kamar.store');
    Route::get('/admin/kamar/edit/{id}', [KamarController::class, 'edit'])->name('kamar.edit');
    Route::post('/admin/kamar/update/{id}', [KamarController::class, 'update'])->name('kamar.update');
    Route::get('/admin/kamar/hapus/{id}', [KamarController::class, 'destroy'])->name('kamar.destroy');
    
    // Alternative kamar admin routes (for price & photo management)
    Route::get('/admin/kamar/manage', [KamarAdminController::class, 'index'])->name('admin.kamar.index');
    Route::post('/admin/kamar/manage/{id}', [KamarAdminController::class, 'update'])->name('admin.kamar.update');

    /*
    |--------------------------------------------------------------------------
    | PENGUNJUNG Management
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/pengunjung', [PengunjungController::class, 'index'])->name('pengunjung.index');
    Route::get('/admin/pengunjung/show/{id}', [PengunjungController::class, 'show'])->name('pengunjung.show');
    Route::get('/admin/pengunjung/pending', [PengunjungController::class, 'pending'])->name('pengunjung.pending');
    Route::get('/admin/pembayaran/konfirmasi', [PengunjungController::class, 'konfirmasiPembayaran'])->name('pembayaran.konfirmasi');
    Route::post('/admin/pengunjung/{id}/upload-payment', [PengunjungController::class, 'uploadPayment'])->name('pengunjung.upload_payment');
    Route::post('/admin/pengunjung/{id}/approve', [PengunjungController::class, 'approve'])->name('pengunjung.approve');
    Route::post('/admin/pengunjung/{id}/reject', [PengunjungController::class, 'reject'])->name('pengunjung.reject');
    Route::get('/admin/pengunjung/tambah', [PengunjungController::class, 'create'])->name('pengunjung.create');
    Route::post('/admin/pengunjung/simpan', [PengunjungController::class, 'store'])->name('pengunjung.store');
    Route::get('/admin/pengunjung/edit/{id}', [PengunjungController::class, 'edit'])->name('pengunjung.edit');
    Route::post('/admin/pengunjung/update/{id}', [PengunjungController::class, 'update'])->name('pengunjung.update');
    Route::get('/admin/pengunjung/hapus/{id}', [PengunjungController::class, 'destroy'])->name('pengunjung.destroy');
    
    // Check-in / Check-out
    Route::get('/admin/pengunjung/{id}/checkin', [PengunjungController::class, 'showCheckin'])->name('pengunjung.checkin');
    Route::post('/admin/pengunjung/{id}/checkin', [PengunjungController::class, 'processCheckin'])->name('pengunjung.checkin.process');
    Route::post('/admin/pengunjung/{id}/checkout', [PengunjungController::class, 'processCheckout'])->name('pengunjung.checkout');

    /*
    |--------------------------------------------------------------------------
    | ADMIN REPORTS
    |--------------------------------------------------------------------------
    */
    // Booking export PDF
    Route::get('/admin/bookings/pdf', [BookingController::class, 'exportPdf'])->name('booking.pdf');
    
    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/report/pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('report.pdf');
    Route::get('/admin/report/csv', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('report.csv');
    Route::get('/admin/report/monthly', [\App\Http\Controllers\ReportController::class, 'monthly'])->name('report.monthly');
    Route::get('/admin/report/monthly/pdf', [\App\Http\Controllers\ReportController::class, 'exportMonthlyPdf'])->name('report.monthly.pdf');
    Route::get('/admin/report/monthly/csv', [\App\Http\Controllers\ReportController::class, 'exportMonthlyCsv'])->name('report.monthly.csv');
});

