<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wi_finishes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('oka')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('proyek')->nullable();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->string('part_no')->nullable();
            $table->foreignUuid('wi_trn_id')->nullable()->index();
            $table->decimal('qty_fin', 12, 4)->nullable();
            $table->date('tgl_fin')->nullable();
            $table->string('jenis_kereta')->nullable();
            $table->foreignUuid('jenis_kereta_id')->nullable()->index();
            $table->boolean('cetak')->nullable()->default(false);
            $table->string('no_sttp')->nullable();
            $table->foreignUuid('sttpp_id')->nullable()->index();
            $table->string('pengirim')->nullable();
            $table->foreignId('pengirim_id')->nullable()->index();
            $table->string('penerima')->nullable();
            $table->foreignId('penerima_id')->nullable()->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wi_finishes');
    }
};
