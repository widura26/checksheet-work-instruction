<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daftar_kebutuhan_materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wbs');
            $table->string('sheet_asal');
            $table->string('no');
            $table->string('rev');
            $table->string('kode_material');
            $table->string('kode_material_konversi')->nullable();
            $table->string('kode_material_delete');
            $table->string('deskripsi_material');
            $table->string('satuan');
            $table->string('group');
            $table->string('lot');
            $table->string('kebutuhan');
            $table->string('tindak_lanjut');
            $table->string('no_pr');
            $table->string('no_po')->nullable();
            $table->string('qty_pr')->nullable();
            $table->string('status')->nullable();
            $table->string('deliv_date_sap')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_kebutuhan_materials');
    }
};
