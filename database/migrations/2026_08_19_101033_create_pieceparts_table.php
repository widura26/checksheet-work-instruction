<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pieceparts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_piecepart')->unique();
            $table->string('no_piecepart')->unique();
            $table->foreignUuid('piecepart_datang_id')->nullable()->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('group1')->nullable();
            $table->string('qty_car')->nullable();
            $table->string('qty_datang1')->nullable();
            $table->string('qty_ambil')->nullable();
            $table->string('qty_total')->nullable();
            $table->text('ket_piecepart')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieceparts');
    }
};