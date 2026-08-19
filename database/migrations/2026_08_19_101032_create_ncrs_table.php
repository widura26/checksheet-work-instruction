<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ncrs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_ncr')->unique();
            $table->foreignUuid('piecepart_datang_id')->nullable()->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('qty_ncr')->nullable();
            $table->date('tgl_ncr')->nullable();
            $table->boolean('diperbaiki')->nullable()->default(false);
            $table->boolean('buatbaru')->nullable()->default(false);
            $table->text('ket_ncr')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ncrs');
    }
};