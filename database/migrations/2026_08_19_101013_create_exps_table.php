<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_exp')->nullable()->unique();
            $table->foreignUuid('cp_detail_id')->nullable()->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->date('tgl_kembali')->nullable();
            $table->foreignId('pengirim_id')->nullable()->index();
            $table->foreignId('penerima_id')->nullable()->index();
            $table->string('tujuan')->nullable();
            $table->text('ket_exp')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exps');
    }
};
