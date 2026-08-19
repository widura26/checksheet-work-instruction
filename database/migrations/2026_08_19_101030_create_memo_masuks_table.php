<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memo_masuks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomer_memo')->unique();
            $table->string('bag_pengirim')->nullable();
            $table->string('perihal')->nullable();
            $table->string('penerima')->nullable();
            $table->string('pemroses')->nullable();
            $table->date('tgl_terima')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('nama_file')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memo_masuks');
    }
};