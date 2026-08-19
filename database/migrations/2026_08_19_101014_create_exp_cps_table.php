<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exp_cps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('exp_cp')->nullable()->unique();
            $table->foreignUuid('cp_detail_id')->nullable()->index();
            $table->string('nama_komp')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('destinasi')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->foreignId('pengirim_id')->nullable()->index();
            $table->foreignId('penerima_id')->nullable()->index();
            $table->date('tgl_terima')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exp_cps');
    }
};
