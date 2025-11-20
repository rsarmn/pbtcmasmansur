<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PengunjungController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// AUTH
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

// ADMIN routes — gunakan FQCN middleware untuk menghindari alias yang belum terdaftar
Route::middleware([\App\Http\Middleware\AdminAuth::class])->group(function () {

    // Dashboard Admin -> point to controller's dashboard() method
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Kamar
    Route::get('/admin/kamar', [KamarController::class, 'index'])->name('kamar.index');
    Route::get('/admin/kamar/tambah', [KamarController::class, 'create'])->name('kamar.create');
    Route::post('/admin/kamar/simpan', [KamarController::class, 'store'])->name('kamar.store');
    Route::get('/admin/kamar/edit/{id}', [KamarController::class, 'edit'])->name('kamar.edit');
    Route::post('/admin/kamar/update/{id}', [KamarController::class, 'update'])->name('kamar.update');
    Route::get('/admin/kamar/hapus/{id}', [KamarController::class, 'destroy'])->name('kamar.destroy');

    // Pengunjung
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

    // Booking
    Route::get('/admin/booking/corporate', [BookingController::class, 'corporate'])->name('booking.corporate');
    Route::post('/admin/booking/corporate/simpan', [BookingController::class, 'storeCorporate'])->name('booking.corporate.store');

    Route::get('/admin/booking/individu', [BookingController::class, 'individu'])->name('booking.individu');
    Route::post('/admin/booking/individu/simpan', [BookingController::class, 'storeIndividu'])->name('booking.individu.store');
    // Booking export PDF
    Route::get('/admin/bookings/pdf', [BookingController::class, 'exportPdf'])->name('booking.pdf');
    // Reports: PDF / CSV
    Route::get('/admin/report/pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('report.pdf');
    Route::get('/admin/report/csv', [\App\Http\Controllers\ReportController::class, 'exportCsv'])->name('report.csv');
    // Admin monthly report
    Route::get('/admin/report/monthly', [\App\Http\Controllers\ReportController::class, 'monthly'])->name('report.monthly');
    Route::get('/admin/report/monthly/pdf', [\App\Http\Controllers\ReportController::class, 'exportMonthlyPdf'])->name('report.monthly.pdf');
    Route::get('/admin/report/monthly/csv', [\App\Http\Controllers\ReportController::class, 'exportMonthlyCsv'])->name('report.monthly.csv');
});

// Public user status page (server-rendered) - for testing only
Route::get('/user/test', function(){
    $totalKamar = \App\Models\Kamar::count();
    $kamarKosong = \App\Models\Kamar::where('status','kosong')->count();
    $jumlahPengunjung = \App\Models\Pengunjung::count();
    $pendingPayments = \App\Models\Pengunjung::where('payment_status','pending')->count();
    return view('user.status', compact('totalKamar','kamarKosong','jumlahPengunjung','pendingPayments'));
})->name('user.test');