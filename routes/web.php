<?php

use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CheckSheetController;
use App\Http\Controllers\CpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpedisiController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\KodeMaterialController;
use App\Http\Controllers\MdController;
use App\Http\Controllers\DaftarKaryawanController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\OkaController;
use App\Http\Controllers\ProsesController;
use App\Http\Controllers\ProjectCodeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SttpController;
use App\Http\Controllers\WiController;
use App\Http\Controllers\WiLapController;
use App\Http\Controllers\DistribusiController;
// use App\Http\Livewire\Distribusi\CreateForm;
// use App\Http\Livewire\Distribusi\PenyerahanCreateForm;
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
    Route::prefix('kode-material')->group(function () {
        Route::get('/', [KodeMaterialController::class, 'index'])->name('kode-material.index');
        Route::get('/data', [KodeMaterialController::class, 'data'])->name('kode-material.data');
        Route::get('/{kodeMaterial}/edit', [KodeMaterialController::class, 'edit'])->name('kode-material.edit');
        Route::put('/{kodeMaterial}', [KodeMaterialController::class, 'update'])->name('kode-material.update');
        Route::delete('/{kodeMaterial}', [KodeMaterialController::class, 'destroy'])->name('kode-material.destroy');
    });
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
        Route::post('/', [MdController::class, 'store'])->name('md.store');
        Route::put('/{mdr}', [MdController::class, 'update'])->name('md.update');
        Route::delete('/{mdr}', [MdController::class, 'destroy'])->name('md.destroy');
    });

    Route::prefix('cutting-plan')->group(function () {
        Route::get('/', [CpController::class, 'index'])->name('cp.index');
        Route::get('/data', [CpController::class, 'getData'])->name('cp.data');
        Route::post('/', [CpController::class, 'store'])->name('cp.store');
        Route::put('/{cp}', [CpController::class, 'update'])->name('cp.update');
        Route::delete('/{cp}', [CpController::class, 'destroy'])->name('cp.destroy');
    });

    Route::prefix('work-instruction')->group(function () {
        Route::get('/', [WiController::class, 'index'])->name('wi.index');
        Route::get('/data', [WiController::class, 'data'])->name('wi.data');
        Route::post('/', [WiController::class, 'store'])->name('wi.store');
        Route::put('/{wiTrn}', [WiController::class, 'update'])->name('wi.update');
        Route::delete('/{wiTrn}', [WiController::class, 'destroy'])->name('wi.destroy');
    });

    Route::get('/check-sheet', [CheckSheetController::class, 'index'])->name('check-sheet.index');

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

    // User Management
    Route::prefix('users')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/data', [StaffController::class, 'data'])->name('data');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{id}', [StaffController::class, 'update'])->name('update');
        Route::post('/{nip}/status', [StaffController::class, 'changeStatus'])->name('status');
        Route::delete('/{id}', [StaffController::class, 'destroy'])->name('destroy');
    });

    Route::get('roles/data', [RoleController::class, 'data'])->name('roles.data');
    Route::resource('roles', RoleController::class)->names('roles');

    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [DaftarKaryawanController::class, 'index'])->name('index');
        Route::get('/data', [DaftarKaryawanController::class, 'data'])->name('data');
        Route::get('/{karyawan}', [DaftarKaryawanController::class, 'show'])->name('show');
        Route::post('/', [DaftarKaryawanController::class, 'store'])->name('store');
        Route::put('/{karyawan}', [DaftarKaryawanController::class, 'update'])->name('update');
        Route::delete('/{karyawan}', [DaftarKaryawanController::class, 'destroy'])->name('destroy');
        Route::post('/import', [DaftarKaryawanController::class, 'import'])->name('import');
    });

    Route::prefix('lokasi/departemen')->name('lokasi.departemen.')->group(function () {
        Route::get('/', [DepartemenController::class, 'index'])->name('index');
        Route::get('/data', [DepartemenController::class, 'data'])->name('data');
        Route::get('/create', [DepartemenController::class, 'create'])->name('create');
        Route::post('/', [DepartemenController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [DepartemenController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DepartemenController::class, 'update'])->name('update');
        Route::delete('/{id}', [DepartemenController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/distribusi', [DepartemenController::class, 'distribusi'])->name('distribusi');
        Route::get('/{id}/distribusi/data', [DepartemenController::class, 'getDistribusiData'])->name('distribusi.data');
        Route::post('/{departemen}/toggle', [DepartemenController::class, 'toggleDepartemen'])->name('toggle');
    });

    Route::prefix('distribusi')->controller(DistribusiController::class)->group(function () {
        Route::get('/', 'index')->name('distribusi.index');
        Route::get('/datatable', 'datatable')->name('distribusi.datatable');
        Route::get('/export', 'exportExcel')->name('distribusi.export');
        Route::get('/{distribusiTool}', 'show')->name('distribusi.show');
        // Penyerahan
        // Route::get('/penyerahan/create', PenyerahanCreateForm::class)->name('distribusi.penyerahan.create');
        Route::post('/batalkan/{distribusiTool}/penyerahan', 'batalkanPenyerahan')->name('distribusi.batalkan-penyerahan')->middleware('client.request.id');
        // Peminjaman
        // Route::get('/peminjaman/create', CreateForm::class)->name('distribusi.peminjaman.create');
        Route::get('/peminjaman/{distribusiTool}/kembali', 'showPengembalian')->name('distribusi.peminjaman.kembali');
        Route::post('/peminjaman/{distribusiTool}/kembali', 'storePengembalian')->name('distribusi.peminjaman.storeKembali')->middleware('client.request.id');

        // API routes
        Route::get('/api/bagian/{departemen_id}', 'getBagian')->name('distribusi.api.bagian');
        Route::get('/api/unit/{bagian_id}', 'getUnit')->name('distribusi.api.unit');
        // cetak berita acara
        Route::get('/print/{distribusiTool}/berita-acara', 'beritaAcara')->name('distribusi.berita-acara');
        // upload file bast
        Route::post('/distribusi/upload-bast', [DistribusiController::class, 'uploadBast'])->name('distribusi.upload-bast')->middleware('client.request.id');
        // unduh file bast ttd
        Route::get('/distribusi/unduh-bast-ttd/{distribusiTool}', [DistribusiController::class, 'downloadBastFile'])->name('distribusi.unduh-bast-ttd');
    });
});
