<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AsnController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\HariLiburController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\CheckApiToken;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Group Route yang butuh Login
Route::middleware([CheckApiToken::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Pegawai
    Route::resource('asn', AsnController::class);
    Route::get('/asn-template', [AsnController::class, 'downloadTemplate'])->name('asn.template');
    Route::post('/asn-import', [AsnController::class, 'import'])->name('asn.import');
    Route::delete('/asn/{id}/reset-device', [AsnController::class, 'resetDevice'])->name('asn.reset-device');

    // Manajemen Shift
    Route::resource('shift', ShiftController::class);

    // Manajemen Hari Libur
    Route::resource('hari-libur', HariLiburController::class);

    // Manajemen Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/generate', [JadwalController::class, 'createGenerate'])->name('jadwal.generate');
    Route::post('/jadwal/generate', [JadwalController::class, 'storeGenerate'])->name('jadwal.store-generate');
    Route::get('/jadwal/import', function () { return view('jadwal.import'); })->name('jadwal.import.view'); // View Import
    Route::get('/jadwal/template', [JadwalController::class, 'downloadTemplate'])->name('jadwal.template'); // Download Template
    Route::post('/jadwal/import', [JadwalController::class, 'import'])->name('jadwal.import'); // Action Import
    Route::get('/jadwal/{id}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
    Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    Route::delete('/jadwal/date/bulk', [JadwalController::class, 'destroyDate'])->name('jadwal.destroy-date');

    // Manajemen Banner
    Route::get('/banner', [BannerController::class, 'index'])->name('banner.index');
    Route::get('/banner/create', [BannerController::class, 'create'])->name('banner.create');
    Route::post('/banner', [BannerController::class, 'store'])->name('banner.store');
    Route::put('/banner/{id}/toggle', [BannerController::class, 'toggle'])->name('banner.toggle');

    // Manajemen Organisasi
    Route::get('/organisasi', [OrganisasiController::class, 'index'])->name('organisasi.index');
    Route::put('/organisasi/info', [OrganisasiController::class, 'updateInfo'])->name('organisasi.update-info'); // Route Baru
    Route::get('/organisasi/lokasi/create', [OrganisasiController::class, 'createLokasi'])->name('organisasi.create-lokasi');
    Route::post('/organisasi/lokasi', [OrganisasiController::class, 'storeLokasi'])->name('organisasi.store-lokasi');
    Route::get('/organisasi/{id}/edit', [OrganisasiController::class, 'edit'])->name('organisasi.edit');
    Route::put('/organisasi/{id}', [OrganisasiController::class, 'updateLokasi'])->name('organisasi.update');
    Route::delete('/organisasi/{id}', [OrganisasiController::class, 'destroyLokasi'])->name('organisasi.destroy-lokasi');

    // Manajemen Role & Permission
    Route::resource('role', RoleController::class);
});