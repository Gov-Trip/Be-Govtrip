<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'registerView'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return redirect('/pegawai');
});

// Pegawai Module
Route::prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/', [DashboardController::class, 'pegawaiDashboard'])->name('dashboard');
    Route::get('/pengajuan', [DashboardController::class, 'pegawaiPengajuan'])->name('pengajuan');
    Route::post('/pengajuan', [DashboardController::class, 'pegawaiStorePengajuan'])->name('pengajuan.store');
    Route::get('/perjalanan', [DashboardController::class, 'pegawaiPerjalanan'])->name('perjalanan');
    Route::get('/sppd', [DashboardController::class, 'pegawaiSppd'])->name('sppd');
    Route::get('/lpj', [DashboardController::class, 'pegawaiLpj'])->name('lpj');
    Route::post('/lpj', [DashboardController::class, 'pegawaiStoreLpj'])->name('lpj.store');
    Route::get('/draft', [DashboardController::class, 'pegawaiDraft'])->name('draft');
    Route::get('/notifikasi', [DashboardController::class, 'pegawaiNotifikasi'])->name('notifikasi');
    Route::get('/profil', [DashboardController::class, 'pegawaiProfil'])->name('profil');
});

// Atasan Module
Route::prefix('atasan')->name('atasan.')->group(function () {
    Route::get('/', [DashboardController::class, 'atasanDashboard'])->name('dashboard');
    Route::get('/inbox', [DashboardController::class, 'atasanInbox'])->name('inbox');
    Route::post('/decision', [DashboardController::class, 'atasanDecision'])->name('decision.post');
    Route::get('/tim', [DashboardController::class, 'atasanTim'])->name('tim');
    Route::get('/riwayat', [DashboardController::class, 'atasanRiwayat'])->name('riwayat');
    Route::get('/notifikasi', [DashboardController::class, 'atasanNotifikasi'])->name('notifikasi');
});

// Bendahara (Finance) Module
Route::prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/', [DashboardController::class, 'bendaharaDashboard'])->name('dashboard');
    Route::get('/verifikasi', [DashboardController::class, 'bendaharaVerifikasi'])->name('verifikasi');
    Route::post('/verifikasi', [DashboardController::class, 'bendaharaVerify'])->name('verifikasi.post');
    Route::get('/pencairan', [DashboardController::class, 'bendaharaPencairan'])->name('pencairan');
    Route::get('/anggaran', [DashboardController::class, 'bendaharaAnggaran'])->name('anggaran');
    Route::get('/logistik', [DashboardController::class, 'bendaharaLogistik'])->name('logistik');
    Route::get('/lpj', [DashboardController::class, 'bendaharaLpj'])->name('lpj');
    Route::get('/riwayat', [DashboardController::class, 'bendaharaRiwayat'])->name('riwayat');
    Route::get('/notifikasi', [DashboardController::class, 'bendaharaNotifikasi'])->name('notifikasi');
});

// Admin Travel Module
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/pegawai', [DashboardController::class, 'adminPegawai'])->name('pegawai');
    Route::get('/perjalanan', [DashboardController::class, 'adminPerjalanan'])->name('perjalanan');
    Route::post('/logistics', [DashboardController::class, 'adminStoreLogistics'])->name('logistics.store');
    Route::get('/dokumen', [DashboardController::class, 'adminDokumen'])->name('dokumen');
    Route::get('/sppd', [DashboardController::class, 'adminSppd'])->name('sppd');
    Route::get('/monitoring', [DashboardController::class, 'adminMonitoring'])->name('monitoring');
    Route::get('/notifikasi', [DashboardController::class, 'adminNotifikasi'])->name('notifikasi');
});

// Super Admin Module
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/', [DashboardController::class, 'superadminDashboard'])->name('dashboard');
    Route::get('/pengguna', [DashboardController::class, 'superadminPengguna'])->name('pengguna');
    Route::get('/akses', [DashboardController::class, 'superadminAkses'])->name('akses');
    Route::get('/konfigurasi', [DashboardController::class, 'superadminKonfigurasi'])->name('konfigurasi');
    Route::get('/audit', [DashboardController::class, 'superadminAudit'])->name('audit');
    Route::get('/unit', [DashboardController::class, 'superadminUnit'])->name('unit');
    Route::get('/notifikasi', [DashboardController::class, 'superadminNotifikasi'])->name('notifikasi');
});
