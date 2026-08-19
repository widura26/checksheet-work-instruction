<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piecepart_datangs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_ppart_datang')->unique();
            $table->string('sjn_ppart')->unique();
            $table->string('batc')->nullable();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('no_part2')->nullable();
            $table->string('nama_part')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('qty_datang')->nullable();
            $table->date('tgl_datang')->nullable();
            $table->text('ket_ppart_datang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piecepart_datangs');
    }
};