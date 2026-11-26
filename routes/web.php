<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BillOfMaterialController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\BomDocumentRevisionController;
use App\Http\Controllers\ProjectCodeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SapDatasetController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'auth.active'])->group(function () {

    // Dashboard Material Planning (admin & viewer)
    Route::prefix('material-planning')->group(function () {
        Route::get('/', [SapDatasetController::class, 'index'])->name('sap-dataset.index');
        Route::get('/data', [SapDatasetController::class, 'data'])->middleware('throttle:120,1')->name('material-planning.data');
        Route::get('/options', [SapDatasetController::class, 'options'])->middleware('throttle:120,1')->name('material-planning.options');
        Route::get('/export', [SapDatasetController::class, 'export'])->middleware('throttle:30,1')->name('material-planning.export');
    });

    // Aksi upload/import dan pengelolaan data hanya untuk admin
    Route::middleware('role:admin')->group(function () {
        Route::prefix('material-planning')->group(function () {
            Route::get('/import-status/{importId}', [SapDatasetController::class, 'importStatus'])->middleware('throttle:120,1')->name('sap-dataset.import-status');
            Route::post('/import-excel', [SapDatasetController::class, 'import_excel'])->middleware('throttle:10,1')->name('sap-dataset.import-excel');
        });

        Route::prefix('project-code')->group(function () {
            Route::get('/', [ProjectCodeController::class, 'index'])->name('project-code.index');
            Route::get('/data', [ProjectCodeController::class, 'getData'])->name('project-code.data');
            Route::get('/{project}/edit', [ProjectCodeController::class, 'edit'])->name('project-code.edit');
            Route::put('/{project}', [ProjectCodeController::class, 'update'])->name('project-code.update');
        });

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

        Route::prefix('activity-log')->name('activity-log.')->group(function () {
            Route::get('/', [ActivityLogController::class, 'index'])->name('index');
            Route::get('/data', [ActivityLogController::class, 'data'])->name('data');
            Route::get('/{activity}', [ActivityLogController::class, 'show'])
                ->whereNumber('activity')
                ->name('show');
        });

        Route::prefix('bill-of-material')->group(function () {
            Route::get('/', [BomController::class, 'index'])->name('bill-of-material.index');
            Route::get('/data', [BomController::class, 'getData'])->name('bill-of-material.data');
            Route::get('/create', [BomController::class, 'create'])->name('bill-of-material.create');
            Route::post('/', [BomController::class, 'store'])->name('bill-of-material.store');
            Route::get('/{id}/edit', [BomController::class, 'edit'])->name('bill-of-material.edit');
            Route::put('/{id}', [BomController::class, 'update'])->name('bill-of-material.update');
            Route::delete('/{id}/delete', [BomController::class, 'delete'])->name('bill-of-material.delete');

            Route::prefix('document')->group(function () {
                Route::get('/{id}', [BomDocumentRevisionController::class, 'show'])->name('bom-document-revision.index');
                Route::delete('/{id}', [BomDocumentRevisionController::class, 'delete'])->name('bom-document-revision.delete');
                Route::get('/{id}/download', [BomDocumentRevisionController::class, 'download'])->name('bom-document-revision.download');
            });
        });

        Route::prefix('bom')->name('bom.')->group(function () {
            Route::post('/import', [BillOfMaterialController::class, 'import'])->middleware('throttle:10,1')->name('import');
            Route::get('/status/{importId}', [BillOfMaterialController::class, 'status'])->middleware('throttle:120,1')->name('status');
        });

        Route::delete('/reset-data', [SapDatasetController::class, 'resetData'])->name('reset-data');
    });
});
