<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sttp_dalmat_datangs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_sttp_dalmat_datang')->unique();
            $table->string('no_sjn')->unique();
            $table->string('no_sttp')->unique();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('nama_part')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->foreignUuid('project_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->bigInteger('qty_datang')->nullable();
            $table->date('tgl_datang')->nullable();
            $table->string('satuan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sttp_dalmat_datangs');
    }
};
