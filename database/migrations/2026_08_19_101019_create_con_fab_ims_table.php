<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('con_fab_ims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->nullable();
            $table->string('spesifikasi')->nullable();
            $table->string('kode_alat')->unique();
            $table->foreignUuid('mesin_id')->nullable()->index();
            $table->bigInteger('jumlah')->nullable();
            $table->string('satuan')->nullable();
            $table->string('kondisi')->nullable();
            $table->string('lokasi')->nullable();
            $table->string('no_rak')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('con_fab_ims');
    }
};
