<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_doc')->unique();
            $table->string('nama_doc')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('wbs')->nullable();
            $table->string('jenis_doc')->nullable();
            $table->date('tgl_terima')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->string('revisi')->nullable();
            $table->string('bag_penerima')->nullable();
            $table->foreignUuid('exp_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pipts');
    }
};