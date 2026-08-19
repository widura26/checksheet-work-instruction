<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_botol')->unique();
            $table->string('vendor')->nullable();
            $table->string('no_spp')->nullable();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->date('tgl_masuk')->nullable();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->uuid('spm_id')->nullable()->index();
            $table->date('tgl_bpm')->nullable();
            $table->date('tgl_spm')->nullable();
            $table->string('bagian')->nullable();
            $table->string('tb_isi')->nullable();
            $table->string('tb_kosong')->nullable();
            $table->string('tek_awal')->nullable();
            $table->string('tek_akhir')->nullable();
            $table->string('no_ambil')->nullable();
            $table->date('tgl_ambil')->nullable();
            $table->string('no_keluar')->nullable();
            $table->date('tgl_keluar')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gases');
    }
};
