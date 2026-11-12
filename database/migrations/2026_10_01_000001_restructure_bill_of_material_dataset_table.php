<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('bill_of_material_dataset');

        Schema::create('bill_of_material_dataset', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sheet_name')->nullable();
            $table->string('kode_material')->nullable();
            $table->string('kode_material_delete')->nullable();
            $table->string('kode_material_stock')->nullable();
            $table->string('material_komponen')->nullable();
            $table->string('spesifikasi')->nullable();
            $table->string('qty_ts_series')->nullable();
            $table->string('uom_car')->nullable();
            $table->string('qty_total_series_allowance')->nullable();
            $table->string('qty_pr_total')->nullable();
            $table->enum('status', ['valid', 'invalid'])->default('valid');
            $table->string('import_id');
            $table->timestamps();

            $table->index('import_id');
            $table->index('sheet_name');
            $table->index('kode_material');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_of_material_dataset');

        Schema::create('bill_of_material_dataset', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bom_id')->nullable();
            $table->string('sheet_name');
            $table->string('takt')->nullable();
            $table->string('rev_bom')->nullable();
            $table->integer('no')->nullable();
            $table->string('kode_material')->nullable();
            $table->string('kode_material_stock')->nullable();
            $table->enum('status', ['valid', 'invalid'])->default('valid');
            $table->string('import_id');
            $table->timestamps();

            $table->index('import_id');
            $table->index('sheet_name');
            $table->index('kode_material');
        });
    }
};
