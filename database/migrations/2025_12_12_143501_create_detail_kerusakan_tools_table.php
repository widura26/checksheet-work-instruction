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
        Schema::create('detail_kerusakan_tools', function (Blueprint $table) {
            $table->id();
            $table->string('no_spr')->nullable();
            $table->foreignId('detail_daftar_distribusi_tools_id')->constrained('detail_daftar_distribusi_tools');
            $table->text('deskripsi_kerusakan');
            $table->enum('status_pengajuan_spr', ['sudah', 'belum']);
            $table->date('tanggal_spr')->nullable();
            $table->string('file_spr')->nullable();
            $table->enum('status_perbaikan', ['menunggu_spr', 'sedang_diperbaiki', 'selesai', 'tidak_bisa_diperbaiki'])->default('menunggu_spr');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_kerusakan_tools');
    }
};
