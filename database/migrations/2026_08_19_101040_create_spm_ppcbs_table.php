<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spm_ppcbs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_spm_ppcb')->unique();
            $table->string('no_spm_ppcb')->unique();
            $table->foreignUuid('cp_detail_id')->nullable()->index();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->bigInteger('qty_bpm_ppcb')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->foreignUuid('project_id')->nullable()->index();
            $table->string('lot_ppcb')->nullable();
            $table->bigInteger('qty_spm_ppcb')->nullable();
            $table->foreignId('pengirim_id')->nullable()->index();
            $table->foreignId('mengetahui_id')->nullable()->index();
            $table->foreignId('penerima_id')->nullable()->index();
            $table->date('tgl_spm')->nullable();
            $table->text('ket_spm_ppcb')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spm_ppcbs');
    }
};
