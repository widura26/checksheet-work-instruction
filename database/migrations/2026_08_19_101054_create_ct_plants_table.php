<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ct_plants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('oka')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('kode_proyek')->nullable();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->string('no_ctplan')->nullable();
            $table->foreignUuid('cp_detail_id')->nullable()->index();
            $table->string('bpm_no')->nullable();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->string('idbarang')->nullable();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->date('tgl')->nullable();
            $table->bigInteger('detail')->nullable();
            $table->string('status')->nullable();
            $table->string('jenis_kereta')->nullable();
            $table->foreignUuid('jenis_kereta_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ct_plants');
    }
};