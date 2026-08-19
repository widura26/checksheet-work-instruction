<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kode_bagian_spm', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('jabatan');
            $table->string('nama_manager');
            $table->integer('stok_rendah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_bagian_spm');
    }
};
