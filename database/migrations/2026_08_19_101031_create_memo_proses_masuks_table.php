<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_proses_masuks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_proses_masuk')->unique();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->string('spec_mat_memo')->nullable();
            $table->string('qty')->nullable();
            $table->string('satuan')->nullable();
            $table->uuid('spm_id')->nullable()->index();
            $table->string('kode_mat_spp')->nullable();
            $table->string('spec_mat_spp')->nullable();
            $table->string('qty_spp')->nullable();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->string('kode_mat_bpm')->nullable();
            $table->string('spec_mat_bpm')->nullable();
            $table->string('qty_bpm')->nullable();
            $table->date('tgl_memo_bpm')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_proses_masuks');
    }
};