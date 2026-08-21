<?php

use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpedisiController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\KodeMaterialController;
use App\Http\Controllers\MdController;
use App\Http\Controllers\OkaController;
use App\Http\Controllers\ProsesController;
use App\Http\Controllers\ProjectCodeController;
use App\Http\Controllers\SttpController;
use App\Http\Controllers\WiController;
use App\Http\Controllers\WiLapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Berisi route untuk menu Checksheet Work Instruction.
| Login / logout ditangani oleh Laravel Fortify.
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'auth.active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/grafik', [GrafikController::class, 'index'])->name('grafik.index');
    Route::get('/oka', [OkaController::class, 'index'])->name('oka.index');
    Route::get('/kode-material', [KodeMaterialController::class, 'index'])->name('kode-material.index');
    Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.index');

    Route::prefix('project-code')->group(function(){
        Route::get('/', [ProjectCodeController::class, 'index'])->name('project-code.index');
        Route::get('/data', [ProjectCodeController::class, 'getData'])->name('project-code.data');
        Route::get('/{project}/edit', [ProjectCodeController::class, 'edit'])->name('project-code.edit');
        Route::put('/{project}', [ProjectCodeController::class, 'update'])->name('project-code.update');
    });

    Route::prefix("processes")->group(function(){
        Route::get('/', [ProsesController::class, 'index'])->name('processes.index');
        Route::get('/data', [ProsesController::class, 'data'])->name('processes.data');
    });

    Route::prefix('manufacture-drawing')->group(function () {
        Route::get('/', [MdController::class, 'index'])->name('md.index');
        Route::get('/data', [MdController::class, 'data'])->name('md.data');
    });

    Route::prefix('cutting-plan')->group(function () {
        Route::get('/', [CpController::class, 'index'])->name('cp.index');
        Route::get('/data', [CpController::class, 'getData'])->name('cp.data');
    });

    Route::prefix('work-instruction')->group(function () {
        Route::get('/', [WiController::class, 'index'])->name('wi.index');
        Route::get('/data', [WiController::class, 'data'])->name('wi.data');
    });

    Route::prefix('surat-tanda-terima-penyerahan-produk')->group(function () {
        Route::get('/', [SttpController::class, 'index'])->name('sttp.index');
        Route::get('/data', [SttpController::class, 'data'])->name('sttp.data');
    });

    Route::prefix('ekspedisi')->group(function () {
        Route::get('/', [ExpedisiController::class, 'index'])->name('ekspedisi.index');
        Route::get('/data', [ExpedisiController::class, 'data'])->name('ekspedisi.data');
    });

    Route::prefix('laporan')->group(function () {
        Route::get('/work-instruction', [WiLapController::class, 'index'])->name('laporan-work-instruction.index');
        Route::get('/surat-permintaan-material', [WiLapController::class, 'index'])->name('laporan-surat-permintaan-material.index');
        Route::get('/cutting-plan', [WiLapController::class, 'index'])->name('laporan-cutting-plan.index');
        Route::get('/bon-permintaan-material', [WiLapController::class, 'index'])->name('laporan-bon-permintaan-material.index');
    });
});
