<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sttpp_fabs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_sttp_fab')->unique();
            $table->foreignUuid('sttpp_id')->nullable()->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('name_part')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->foreignUuid('project_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->bigInteger('qty_lot')->nullable();
            $table->bigInteger('qty_kirim')->nullable();
            $table->string('satuan')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->string('dari')->nullable();
            $table->string('ke')->nullable();
            $table->foreignId('pengirim_id')->nullable()->index();
            $table->foreignId('penerima_id')->nullable()->index();
            $table->text('keterangan')->nullable();
            $table->foreignUuid('jenis_kereta_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->date('tgl_input')->nullable();
            $table->string('rev')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sttpp_fabs');
    }
};
