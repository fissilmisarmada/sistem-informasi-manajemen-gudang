<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\RiwayatPenempatanController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
    Route::get('/lupa-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::view('/dashboard/admin', 'dashboard.admin')->middleware('role:admin')->name('dashboard.admin');
    Route::view('/dashboard/staff', 'dashboard.staff')->middleware('role:staff')->name('dashboard.staff');
    Route::view('/dashboard/pimpinan', 'dashboard.pimpinan')->middleware('role:pimpinan')->name('dashboard.pimpinan');

    Route::get('/denah-gudang', [RakController::class, 'denahGudang'])
        ->middleware('role:admin,staff,pimpinan')
        ->name('denah-gudang');

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('role:admin,staff,pimpinan')
        ->name('laporan.index');

    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
        Route::post('/laporan/import', [LaporanController::class, 'import'])->name('laporan.import');
    });

    Route::prefix('riwayat')->name('riwayat.')->group(function () {
        Route::get('/', [RiwayatPenempatanController::class, 'index'])->name('index');
    });

    Route::prefix('buku')->name('buku.')->group(function () {
        Route::get('/', [BukuController::class, 'index'])->name('index');
        Route::get('/cari', [BukuController::class, 'cariBuku'])->name('cari');
        Route::get('/hasil-cari', [BukuController::class, 'hasilCariBuku'])->name('hasil-cari');

        Route::middleware('role:admin,staff')->group(function () {
        Route::get('/tambah', [BukuController::class, 'createData'])->name('create-data');
        Route::post('/simpan', [BukuController::class, 'storeData'])->name('store-data');
        Route::get('/{buku}/edit', [BukuController::class, 'editData'])->name('edit-data');
        Route::put('/{buku}', [BukuController::class, 'updateData'])->name('update-data');
        Route::delete('/{buku}', [BukuController::class, 'destroyData'])->name('delete-data');
        Route::get('/tempatkan', [BukuController::class, 'create'])->name('create');
        Route::post('/cari-isbn', [BukuController::class, 'cariByIsbn'])->name('cari-isbn');
        Route::post('/simpan-baru', [BukuController::class, 'simpanBukuBaru'])->name('simpan-baru');
        Route::post('/tempatkan', [BukuController::class, 'store'])->name('store');
        });

        Route::get('/{buku}', [BukuController::class, 'show'])->name('show');
    });

    Route::prefix('rak')->name('rak.')->group(function () {
        Route::middleware('role:admin,staff')->group(function () {
            Route::get('/', [RakController::class, 'index'])->name('index');
            Route::post('/', [RakController::class, 'store'])->name('store');
        });

        Route::middleware('role:admin')->group(function () {
            Route::put('/{rak}', [RakController::class, 'update'])->name('update');
            Route::delete('/{rak}', [RakController::class, 'destroy'])->name('destroy');
        });

        Route::middleware('role:admin,staff,pimpinan')->group(function () {
            Route::get('/{rak}', [RakController::class, 'show'])->name('show');
        });
    });

    Route::prefix('stock-opname')->name('stock-opname.')->middleware('role:staff')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::get('/{rak}/create', [StockOpnameController::class, 'create'])->name('create');
        Route::post('/{rak}', [StockOpnameController::class, 'store'])->name('store');
        Route::get('/{rak}/riwayat', [StockOpnameController::class, 'riwayat'])->name('riwayat');
    });
});

Route::get('/', function () {
    return redirect()->route('login');
});