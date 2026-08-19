<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sttpps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nosttpp')->unique();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->string('kontrak')->nullable();
            $table->string('lppb')->nullable();
            $table->date('tgl_lppb')->nullable();
            $table->date('tgl_krm')->nullable();
            $table->string('dari')->nullable();
            $table->string('ke')->nullable();
            $table->foreignId('pengirim_id')->nullable()->index();
            $table->foreignId('penerima_id')->nullable()->index();
            $table->bigInteger('detail')->nullable();
            $table->string('tahun')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sttpps');
    }
};
