<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DenahAreaController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MutasiBarangController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PencarianController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\StockOpnameBarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.perform');
    Route::get('/lupa-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->middleware('role:admin')->name('dashboard.admin');
    Route::get('/dashboard/staff', [DashboardController::class, 'staff'])->middleware('role:staff')->name('dashboard.staff');
    Route::get('/dashboard/pimpinan', [DashboardController::class, 'pimpinan'])->middleware('role:pimpinan')->name('dashboard.pimpinan');

    Route::get('/denah-gudang', function () {
        return redirect()->route('denah-area.index');
    })
        ->middleware('role:admin,staff,pimpinan')
        ->name('denah-gudang');

    Route::post('/denah-gudang/assign', [RakController::class, 'assignBarang'])
        ->middleware('role:admin,staff')
        ->name('denah-gudang.assign');

    Route::get('/denah-area', [DenahAreaController::class, 'index'])->middleware('role:admin,staff,pimpinan')->name('denah-area.index');
    Route::get('/denah-area/{denahArea}', [DenahAreaController::class, 'show'])->middleware('role:admin,staff,pimpinan')->name('denah-area.show');
    Route::post('/denah-area/{denahArea}/denah', [DenahAreaController::class, 'uploadDenah'])->middleware('role:admin,staff')->name('denah-area.upload');
    Route::delete('/denah-area/{denahArea}/denah', [DenahAreaController::class, 'hapusDenah'])->middleware('role:admin,staff')->name('denah-area.hapus-denah');
    // Ruangan (denah_area): tulis hanya admin
    Route::post('/denah-area', [DenahAreaController::class, 'store'])->middleware('role:admin')->name('denah-area.store');
    Route::put('/denah-area/{denahArea}', [DenahAreaController::class, 'update'])->middleware('role:admin')->name('denah-area.update');
    Route::patch('/denah-area/{denahArea}/posisi', [DenahAreaController::class, 'updatePosisi'])->middleware('role:admin')->name('denah-area.posisi');
    Route::prefix('denah-area')->name('denah-area.')->middleware('role:admin,staff')->group(function () {
        Route::delete('/{denahArea}', [DenahAreaController::class, 'destroy'])->name('destroy');
        Route::post('/assign', [DenahAreaController::class, 'assignBarang'])->name('assign');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    // Laporan — C6/C7: import admin only, export untuk pimpinan juga
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->middleware('role:admin,staff,pimpinan')->name('laporan.index');
    Route::get('/laporan/export', [LaporanController::class, 'export'])
        ->middleware('role:admin,staff,pimpinan')->name('laporan.export');
    Route::get('/laporan/export-csv', [LaporanController::class, 'exportCsv'])
        ->middleware('role:admin,staff,pimpinan')->name('laporan.export.csv');
    Route::post('/laporan/import', [LaporanController::class, 'import'])
        ->middleware('role:admin')->name('laporan.import');

    // Rak — A3: konsisten hanya admin tulis
    Route::prefix('rak')->name('rak.')->group(function () {
        Route::middleware('role:admin,staff,pimpinan')->group(function () {
            Route::get('/', [RakController::class, 'index'])->name('index');
            Route::get('/{rak}', [RakController::class, 'show'])->name('show');
        });

        Route::middleware('role:admin')->group(function () {
            Route::post('/', [RakController::class, 'store'])->name('store');
            Route::put('/{rak}', [RakController::class, 'update'])->name('update');
            Route::delete('/{rak}', [RakController::class, 'destroy'])->name('destroy');
        });
    });

    // Pencarian global (semua barang gudang)
    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/input-barang', [PencarianController::class, 'input'])->name('pencarian.input');
        Route::get('/input-barang/lookup', [PencarianController::class, 'lookupInput'])->name('pencarian.input.lookup');
        Route::post('/input-barang/proses', [PencarianController::class, 'prosesInput'])->name('pencarian.input.proses');
        Route::post('/input-barang/manual', [PencarianController::class, 'simpanInputManual'])->name('pencarian.input.manual');
    });

    Route::get('/cari', [PencarianController::class, 'index'])
        ->middleware('role:admin,staff,pimpinan')
        ->name('pencarian.index');

    // Kategori barang — A2: tulis hanya admin
    Route::prefix('kategori')->name('kategori.')->middleware('role:admin,staff,pimpinan')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('index');
        Route::middleware('role:admin')->group(function () {
            Route::post('/', [KategoriController::class, 'store'])->name('store');
            Route::put('/{kategori}', [KategoriController::class, 'update'])->name('update');
            Route::delete('/{kategori}', [KategoriController::class, 'destroy'])->name('destroy');
        });
    });

    // Barang gudang — A1: edit/update/destroy hanya admin
    Route::prefix('barang')->name('barang.')->middleware('role:admin,staff,pimpinan')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index');
        Route::get('/stok-menipis', [BarangController::class, 'stokMenipis'])->name('stok-menipis');
        Route::get('/{barang}', [BarangController::class, 'show'])->name('show');

        Route::middleware('role:admin,staff')->group(function () {
            Route::get('/tambah/baru', [BarangController::class, 'create'])->name('create');
            Route::post('/', [BarangController::class, 'store'])->name('store');
        });

        Route::middleware('role:admin')->group(function () {
            Route::get('/{barang}/edit', [BarangController::class, 'edit'])->name('edit');
            Route::put('/{barang}', [BarangController::class, 'update'])->name('update');
            Route::delete('/{barang}', [BarangController::class, 'destroy'])->name('destroy');
        });
    });

    Route::post('/voice/transcribe', [VoiceController::class, 'transcribe'])->middleware('role:admin,staff')->name('voice.transcribe');

    // Web Push subscription — hanya penerima notifikasi (admin + pimpinan)
    Route::middleware('role:admin,pimpinan')->group(function () {
        Route::post('/notifikasi/subscribe', [NotifikasiController::class, 'subscribe'])->name('notifikasi.subscribe');
        Route::delete('/notifikasi/unsubscribe', [NotifikasiController::class, 'unsubscribe'])->name('notifikasi.unsubscribe');
    });

    // Mutasi barang (masuk/keluar)
    Route::prefix('mutasi-barang')->name('mutasi-barang.')->middleware('role:admin,staff,pimpinan')->group(function () {
        Route::get('/', [MutasiBarangController::class, 'index'])->name('index');
        Route::middleware('role:admin,staff')->group(function () {
            Route::get('/tambah', [MutasiBarangController::class, 'create'])->name('create');
            Route::post('/', [MutasiBarangController::class, 'store'])->name('store');
        });
    });

    // Stock opname barang
    Route::prefix('stock-opname-barang')->name('stock-opname-barang.')->middleware('role:admin,staff')->group(function () {
        Route::get('/', [StockOpnameBarangController::class, 'index'])->name('index');
        Route::get('/{barang}/create', [StockOpnameBarangController::class, 'create'])->name('create');
        Route::post('/{barang}', [StockOpnameBarangController::class, 'store'])->name('store');
        Route::get('/{barang}/riwayat', [StockOpnameBarangController::class, 'riwayat'])->name('riwayat');
    });
});

Route::get('/', function () {
    return redirect()->route('login');
});
