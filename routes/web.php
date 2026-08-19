<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CentralToolController;
use App\Http\Controllers\DaftarKaryawanController;
use App\Http\Controllers\DaftarKodeMaterialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartemenController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\ImportDistribusiController;
use App\Http\Controllers\KodeBagianController;
use App\Http\Controllers\ListDokumenPengambilanController;
use App\Http\Controllers\PenggantianToolController;
use App\Http\Controllers\PenguranganToolController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\SatuanKonversiController;
use App\Http\Controllers\SpmController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\stokMaterialController;
use App\Http\Controllers\TindakLanjutToolRusakController;
use App\Http\Controllers\ToolConsumableController;
use App\Http\Controllers\ToolApdController;
use App\Http\Controllers\ToolDibuangController;
use App\Http\Controllers\ToolRusakController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UpdateStockController;
use App\Livewire\Distribusi\Peminjaman\CreateForm;
use App\Livewire\Distribusi\Penyerahan\CreateForm as PenyerahanCreateForm;
use App\Livewire\TransferPosting\Create;
use App\Livewire\TransferPosting\Index;
use App\Livewire\TransferPosting\Show;
use App\Models\Bagian;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware(['auth', 'auth.active'])->group(function () {
    Route::resource('staff', \App\Http\Controllers\StaffController::class);
    Route::post('/staff/status/{nip}', [StaffController::class, 'changeStatus'])->name('staff.status');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/home', function () {
        return redirect('/dashboard');
    });

    Route::controller(DaftarKodeMaterialController::class)->group(function () {
        Route::get('/daftar-kode-material', 'index')->name('daftar-kode-material.index');
        Route::get('/daftar-kode-material/data', 'data')->name('daftar-kode-material.data');
        Route::get('/daftar-kode-material/find', 'findByKode')->name('daftar-kode-material.find');
        Route::post('/daftar-kode-material/import', 'import')->name('daftar-kode-material.import');
        Route::delete('/daftar-kode-material/{id}', 'destroy')->name('daftar-kode-material.destroy');
    });
    Route::middleware('role:admin|staff|viewer')->group(function () {
        // Route::prefix('tool')->name('tool.')->group(function () {
        //     Route::get('/api/tools/find', [ToolsController::class, 'findTool'])->name('tools.find');
        //     Route::get('/search-material', [ToolsController::class, 'searchMaterial'])->name('search-material');
        //     Route::post('/store-or-update', [ToolsController::class, 'storeOrUpdate'])->name('store-or-update');
        //     Route::get('/get-material-detail', [ToolConsumableController::class, 'getMaterialDetail'])->name('get-material-detail');
        // });
        Route::get('/get-material-detail', [stokMaterialController::class, 'getMaterialDetail'])->name('get-material-detail');

        Route::resource('tool', ToolsController::class)->except(['show']);
        // route untuk detail tools seluruh tool
        Route::get('/material/detail/{publicId?}/{projectId?}/{ctId?}', [ToolsController::class, 'show'])->name('tool.show');
        Route::get('/tool-get-data', [ToolsController::class, 'getDataTools'])->name('tool-get-data.data');
        Route::get('/tool-export', [ToolsController::class, 'export'])->name('tool.export');
        // route untuk consumable tools
        Route::resource('consumable', ToolConsumableController::class);
        Route::get('/consumable-export', [ToolConsumableController::class, 'export'])->name('consumable.export');
        // route untuk apd tools
        Route::resource('apd', \App\Http\Controllers\ToolApdController::class);
        Route::get('/apd-export', [ToolApdController::class, 'export'])->name('apd.export');

        Route::prefix('distribusi')->controller(DistribusiController::class)->group(function () {
            Route::get('/', 'index')->name('distribusi.index');
            Route::get('/datatable', 'datatable')->name('distribusi.datatable');
            Route::get('/export', 'exportExcel')->name('distribusi.export');
            Route::get('/{distribusiTool}', 'show')->name('distribusi.show');
            // Penyerahan
            Route::get('/penyerahan/create', PenyerahanCreateForm::class)->name('distribusi.penyerahan.create');
            Route::post('/batalkan/{distribusiTool}/penyerahan', 'batalkanPenyerahan')->name('distribusi.batalkan-penyerahan')->middleware('client.request.id');
            // Peminjaman
            Route::get('/peminjaman/create', CreateForm::class)->name('distribusi.peminjaman.create');
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
        // Tindak Lanjut Tool Rusak
        Route::prefix('/tindak-lanjut-tool-rusak')->controller(TindakLanjutToolRusakController::class)->group(function () {
            Route::get('/', 'index')->name('tindak-lanjut-tool-rusak.index');
            Route::get('/data', 'data')->name('tindak-lanjut-tool-rusak.data');
        });

        // Purchase Requisition Routes
        Route::prefix('/pr')->name('pr.')->controller(PurchaseRequisitionController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/datatable', 'datatable')->name('datatable');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{purchaseRequisition}', 'show')->name('show');
            Route::get('/datang/{purchaseRequisition}', 'sudahDatang')->name('sudah-datang');
            Route::get('/datang-ct/{purchaseRequisition}', 'sudahDatangCt')->name('sudah-datang-ct');
        });

        // Transfer Posting Routes (Livewire)
        Route::get('/transfer-posting', Index::class)->name('transfer-posting.index');
        Route::get('/transfer-posting/create', Create::class)->name('transfer-posting.create');
        Route::get('/transfer-posting/return', \App\Livewire\TransferPosting\ReturnTransfer::class)->name('transfer-posting.return');
        Route::get('/transfer-posting/{materialTransferPosting}', Show::class)->name('transfer-posting.show');

        Route::middleware('role:admin|viewer|staff')->group(function () {
            Route::prefix('lokasi')->name('lokasi.')->group(function () {
                Route::resource('departemen', DepartemenController::class);
                Route::get('departemen-data', [DepartemenController::class, 'data'])->name('departemen.data');
                Route::get('departemen/{id}/distribusi', [DepartemenController::class, 'distribusi'])->name('departemen.distribusi');
                Route::get('departemen/{id}/distribusi-data', [DepartemenController::class, 'getDistribusiData'])->name('departemen.distribusi.data');
                Route::post('departemen/{departemen}/toggle', [DepartemenController::class, 'toggleDepartemen'])->name('departemen.toggle')->middleware('role:admin');

                Route::resource('unit', UnitController::class);
                Route::get('unit-data', [UnitController::class, 'data'])->name('unit.data');
                Route::get('unit/{id}/distribusi', [UnitController::class, 'distribusi'])->name('unit.distribusi');
                Route::get('unit/{id}/distribusi-data', [UnitController::class, 'getDistribusiData'])->name('unit.distribusi.data');
                Route::post('unit/{unit}/toggle', [UnitController::class, 'toggleUnit'])->name('unit.toggle')->middleware('role:admin');

                Route::resource('bagian', BagianController::class);
                Route::get('bagian-data', [BagianController::class, 'getData'])->name('bagian.data');
                Route::get('bagian/{id}/distribusi', [BagianController::class, 'distribusi'])->name('bagian.distribusi');
                Route::get('bagian/{id}/distribusi-data', [BagianController::class, 'getDistribusiData'])->name('bagian.distribusi.data');
                Route::post('bagian/{bagian}/toggle', [BagianController::class, 'toggleBagian'])->name('bagian.toggle')->middleware('role:admin');
            });
        });
        // Project Routes
        Route::resource('projects', \App\Http\Controllers\ProjectController::class);
        Route::get('projects-data', [\App\Http\Controllers\ProjectController::class, 'getData'])->name('projects.data');
        Route::get('projects/{project}/tools-data', [\App\Http\Controllers\ProjectController::class, 'getDataTools'])->name('projects.tools.data');
        Route::post('projects/import', [\App\Http\Controllers\ProjectController::class, 'import'])->name('projects.import');
        Route::post('projects/{project}/close', [ProjectController::class, 'tutupProject'])->name('projects.close');

        Route::prefix('pengurangan-tool')->name('pengurangan-tool.')->middleware('role:admin|staff|staff_ct')->group(function () {
            Route::get('/', [PenguranganToolController::class, 'index'])->name('index');
            Route::get('/data', [PenguranganToolController::class, 'getData'])->name('data');
            Route::get('/create/{publicId}/{projectId}', [PenguranganToolController::class, 'create'])->name('create');
            Route::post('/', [PenguranganToolController::class, 'store'])->name('store')->middleware('client.request.id');
            Route::get('/get-tool-details/{kode_material}', [PenguranganToolController::class, 'getToolDetails'])->name('get-tool-details');
            Route::get('/{penguranganTool}', [PenguranganToolController::class, 'show'])->name('show');
        });
        Route::prefix('update-stok-tool')->name('update-stok-tool.')->middleware('role:admin|staff|viewer')->group(function () {
            Route::get('/', [UpdateStockController::class, 'index'])->name('index');
            Route::get('/data', [UpdateStockController::class, 'getData'])->name('data');
            Route::get('/export', [UpdateStockController::class, 'export'])->name('export');
            Route::get('/create', [UpdateStockController::class, 'create'])->name('create');
            Route::post('/', [UpdateStockController::class, 'store'])->name('store');
            // Enhanced API endpoint for tool details lookup with comprehensive response format
            Route::get('/get-tool-details/{kode_material}', [UpdateStockController::class, 'getToolDetails'])->name('get-tool-details');
        });

        // Tool Rusak Routes
        Route::prefix('/tool-rusak')->name('tool-rusak.')->group(function () {
            Route::get('/datatable', [ToolRusakController::class, 'datatable'])->name('datatable');
            Route::get('/', [ToolRusakController::class, 'index'])->name('index');
            Route::middleware('role:admin|staff')->group(function () {
                Route::post('/{id}/upload-spr', [ToolRusakController::class, 'uploadSpr'])->name('upload-spr');
                Route::patch('/{id}/update-status-perbaikan', [ToolRusakController::class, 'updateStatusPerbaikan'])->name('update-status-perbaikan');
            });
        });

        // SPM routes
        Route::middleware('role:admin|staff|viewer')->group(function () {
            Route::prefix('/spm')->name('spm.')->group(function () {
                Route::get('/', [SpmController::class, 'index'])->name('index');
                Route::get('/create', \App\Livewire\Spm\CreateSpm::class)->name('create');
                Route::get('/getData', [SpmController::class, 'getData'])->name('getData');
                Route::get('/get-projects', [SpmController::class, 'getProjects'])->name('getProjects');
                Route::get('/get-statuses', [SpmController::class, 'getStatuses'])->name('getStatuses');
                Route::post('/upload', [SpmController::class, 'upload'])->name('upload');
                Route::get('/{spm}/download', [SpmController::class, 'download'])->name('download');
                Route::get('/{spm}', [SpmController::class, 'show'])->name('show');
                Route::get('/{spm}/terima', [SpmController::class, 'terima'])->name('terima');
                Route::get('/{spm}/print', [SpmController::class, 'print'])->name('print');
                Route::post('/{spm}/cancel', [SpmController::class, 'cancelSpm'])->name('cancel');
                Route::get('/{spm}/ajukan-ulang', \App\Livewire\Spm\ResubmitSpm::class)->name('ajukan-ulang');
            });
        });
        // List Dokumen Pengambilan Routes
        Route::prefix('/list-dokumen-pengambilan')->name('dokumen-pengambilan.')->middleware('role:admin|staff|viewer')->group(function () {
            Route::get('/', [ListDokumenPengambilanController::class, 'index'])->name('index');
            Route::get('/getData', [ListDokumenPengambilanController::class, 'getData'])->name('getData');
        });
        // Kode Bagian Routes
        Route::prefix('/kode-bagian')->name('kode-bagian.')->middleware('role:admin')->group(function () {
            Route::get('/', [KodeBagianController::class, 'index'])->name('index');
            Route::put('/', [KodeBagianController::class, 'update'])->name('update');
        });
        // Activity Log Routes
        Route::middleware('role:admin')->group(function () {
            Route::prefix('/activity-log')->name('activity-log.')->middleware('role:admin')->group(function () {
                Route::get('/', [ActivityLogController::class, 'index'])->name('index');
                Route::get('/{activity}', [ActivityLogController::class, 'show'])->name('show');
            });
        });
        Route::get('/laporan/distribusi', \App\Livewire\Laporan\Distribusi::class)->name('laporan.distribusi');

        // penggantian tool route
        Route::prefix('/penggantian-tool')->name('penggantian-tool.')->group(function () {
            Route::get('/', [PenggantianToolController::class, 'index'])->name('index');
            Route::get('/data', [PenggantianToolController::class, 'data'])->name('data');
            Route::get('/create', \App\Livewire\PenggantianTool\Create::class)->name('create');
            Route::patch('/update-status', [PenggantianToolController::class, 'updateStatus'])->name('update-status');
        });
        // Posisi CT Routes
        Route::resource('posisi-ct', CentralToolController::class)->middleware('role:admin');
        // upload daftar kode material
        Route::post('/import-material', [DaftarKodeMaterialController::class, 'importMaterial'])->name('import-material')->middleware('role:admin');
        // upload daftar riwayat distribusi
        Route::post('/import-distribusi', [ImportDistribusiController::class, 'importDistribusi'])->name('import-distribusi')->middleware('role:admin');
        // konversi satuan route
        Route::middleware('role:admin')->group(function () {
            Route::controller(SatuanKonversiController::class)->group(function () {
                Route::get('/konversi-satuan', 'index')->name('konversi-satuan.index');
                Route::get('/konversi-satuan/data', 'data')->name('konversi-satuan.data');
                Route::post('/konversi-satuan', 'store')->name('konversi-satuan.store');
                Route::put('/konversi-satuan/{id}', 'update')->name('konversi-satuan.update');
                Route::delete('/konversi-satuan/{id}', 'destroy')->name('konversi-satuan.destroy');
            });
        });
        // print barcode
        Route::middleware('role:admin|staff')->group(function () {
            Route::get('/barcode/print', [BarcodeController::class, 'preview'])->name('barcode.print');
        });
        // daftar Karyawan
        Route::middleware('role:admin|staff|viewer')->group(function () {
            Route::resource('karyawan', DaftarKaryawanController::class)->except(['show', 'create', 'edit']);
            Route::post('/karyawan/import', [DaftarKaryawanController::class, 'import'])->name('karyawan.import')->middleware('role:admin');
        });
        // Tool Dibuang Routes
        Route::prefix('tool-dibuang')->name('tool-dibuang.')->group(function () {
            Route::get('/', [ToolDibuangController::class, 'index'])->name('index');
            Route::get('/datatable', [ToolDibuangController::class, 'datatable'])->name('datatable');
            Route::get('/create', [ToolDibuangController::class, 'create'])->name('create');
            Route::post('/upload-bukti', [ToolDibuangController::class, 'uploadBukti'])->name('upload-bukti');
            Route::get('/export', [ToolDibuangController::class, 'export'])->name('export');
            Route::get('/{toolDibuang}/edit', [ToolDibuangController::class, 'edit'])->name('edit')->middleware('role:admin');
        });
    });
});
