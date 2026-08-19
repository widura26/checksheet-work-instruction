<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpm_cps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->foreignUuid('cp_detail_id')->nullable()->index();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->string('spec')->nullable();
            $table->bigInteger('jumlah_bpm')->nullable();
            $table->text('ket_mat')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->date('tgl_bpm')->nullable();
            $table->text('ket_bpm')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('mat_cp')->nullable();
            $table->string('spek_mat_cp')->nullable();
            $table->bigInteger('qty_cp')->nullable();
            $table->string('mat_sisa')->nullable();
            $table->string('status_mat')->nullable();
            $table->string('no_pr')->nullable();
            $table->string('no_po')->nullable();
            $table->string('eta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpm_cps');
    }
};
