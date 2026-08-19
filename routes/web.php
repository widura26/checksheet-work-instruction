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

    Route::get('/master/oka', [OkaController::class, 'index'])->name('master.oka.index');
    Route::get('/processes', [ProsesController::class, 'index'])->name('processes.index');
    Route::get('/processes/data', [ProsesController::class, 'data'])->name('processes.data');
    Route::get('/master/kode-material', [KodeMaterialController::class, 'index'])->name('master.kode-material.index');

    Route::get('/md', [MdController::class, 'index'])->name('md.index');
    Route::get('/cp', [CpController::class, 'index'])->name('cp.index');
    Route::get('/wi', [WiController::class, 'index'])->name('wi.index');
    Route::get('/barcode', [BarcodeController::class, 'index'])->name('barcode.index');
    Route::get('/sttp', [SttpController::class, 'index'])->name('sttp.index');
    Route::get('/expedisi', [ExpedisiController::class, 'index'])->name('expedisi.index');
    Route::get('/lap-wi', [WiLapController::class, 'index'])->name('lap-wi.index');
});
