<?php

use App\Http\Controllers\BagianController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\BomDocumentRevisionController;
use App\Http\Controllers\BillOfMaterialController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\DaftarKaryawanController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\KodeMaterialController;
use App\Http\Controllers\OkaController;
use App\Http\Controllers\ProjectCodeController;
use App\Http\Controllers\ProsesController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SapDatasetController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UnitController;
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
    Route::get('/oka', [OkaController::class, 'index'])->name('oka.index');
    Route::prefix('kode-material')->group(function () {
        Route::get('/', [KodeMaterialController::class, 'index'])->name('kode-material.index');
        Route::get('/data', [KodeMaterialController::class, 'data'])->name('kode-material.data');
        Route::get('/{kodeMaterial}/edit', [KodeMaterialController::class, 'edit'])->name('kode-material.edit');
        Route::put('/{kodeMaterial}', [KodeMaterialController::class, 'update'])->name('kode-material.update');
        Route::delete('/{kodeMaterial}', [KodeMaterialController::class, 'destroy'])->name('kode-material.destroy');
    });

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

    // User Management
    Route::prefix('users')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/data', [StaffController::class, 'data'])->name('data');
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

    Route::prefix('lokasi/bagian')->name('lokasi.bagian.')->group(function () {
        Route::get('/', [BagianController::class, 'index'])->name('index');
        Route::get('/data', [BagianController::class, 'getData'])->name('data');
        Route::get('/create', [BagianController::class, 'create'])->name('create');
        Route::post('/', [BagianController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BagianController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BagianController::class, 'update'])->name('update');
        Route::delete('/{id}', [BagianController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/distribusi', [BagianController::class, 'distribusi'])->name('distribusi');
        Route::get('/{id}/distribusi/data', [BagianController::class, 'getDistribusiData'])->name('distribusi.data');
        Route::post('/{bagian}/toggle', [BagianController::class, 'toggleBagian'])->name('toggle');
    });

    Route::prefix('lokasi/unit')->name('lokasi.unit.')->group(function () {
        Route::get('/', [UnitController::class, 'index'])->name('index');
        Route::get('/data', [UnitController::class, 'data'])->name('data');
        Route::get('/create', [UnitController::class, 'create'])->name('create');
        Route::post('/', [UnitController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UnitController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UnitController::class, 'update'])->name('update');
        Route::delete('/{id}', [UnitController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/distribusi', [UnitController::class, 'distribusi'])->name('distribusi');
        Route::get('/{id}/distribusi/data', [UnitController::class, 'getDistribusiData'])->name('distribusi.data');
        Route::post('/{unit}/toggle', [UnitController::class, 'toggleUnit'])->name('toggle');
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

    Route::prefix('material-planning')->group(function(){
        Route::get('/', [SapDatasetController::class, 'index'])->name('sap-dataset.index');
        Route::get('/data', [SapDatasetController::class, 'data'])->name('material-planning.data');
        Route::post('/import', [SapDatasetController::class, 'import'])->name('sap-dataset.import');
        Route::post('/import-queue', [SapDatasetController::class, 'importQueue'])->name('sap-dataset.import-queue');
        Route::get('/import-status/{importId}', [SapDatasetController::class, 'importStatus'])->name('sap-dataset.import-status');
        Route::post('/delete', [SapDatasetController::class, 'delete'])->name('sap-dataset.delete');
        Route::get('/deviation', [SapDatasetController::class, 'deviation'])->name('sap-dataset.deviation');
        Route::post('/import-excel', [SapDatasetController::class, 'import_excel'])->name('sap-dataset.import-excel');
    });

    Route::prefix('charts')->group(function(){
        Route::get('/purchase-requisitions', [ChartsController::class, 'purchaseRequisition'])->name('purchase-requisition-chart-data');
        Route::get('/purchase-order', [ChartsController::class, 'purchaseOrder'])->name('purchase-order-chart-data');
        Route::get('/bompr', [ChartsController::class, 'bomprdifference'])->name('bompr-chart-data');
        Route::get('/popr', [ChartsController::class, 'poprdifference'])->name('popr-chart-data');
    });

    Route::prefix('bill-of-material')->group(function(){
        Route::get('/', [BomController::class, 'index'])->name('bill-of-material.index');
        Route::get('/data', [BomController::class, 'getData'])->name('bill-of-material.data');
        Route::get('/create', [BomController::class, 'create'])->name('bill-of-material.create');
        Route::post('/', [BomController::class, 'store'])->name('bill-of-material.store');
        Route::get('/{id}/edit', [BomController::class, 'edit'])->name('bill-of-material.edit');
        Route::put('/{id}', [BomController::class, 'update'])->name('bill-of-material.update');
        Route::delete('/{id}/delete', [BomController::class, 'delete'])->name('bill-of-material.delete');

        Route::prefix('document')->group(function(){
            Route::get('/{id}', [BomDocumentRevisionController::class, 'show'])->name('bom-document-revision.index');
            Route::get('/{id}/delete', [BomDocumentRevisionController::class, 'delete'])->name('bom-document-revision.delete');
            Route::get('/{id}/download', [BomDocumentRevisionController::class, 'download'])->name('bom-document-revision.download');
        });
    });

    Route::prefix('bom')->name('bom.')->group(function () {
        Route::post('/import', [BillOfMaterialController::class, 'import'])->name('import');
        Route::get('/status/{importId}', [BillOfMaterialController::class, 'status'])->name('status');
    });
});
