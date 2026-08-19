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
        Schema::create('distribusi_tools', function (Blueprint $table) {
            $table->id();
            $table->string('nama_peminjam');
            $table->string('nip')->length(50);
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('bagian_id');
            $table->unsignedBigInteger('departemen_id');
            $table->enum('type', ['peminjaman', 'penyerahan']);
            $table->enum('status', ['kembali', 'kembali_sebagian', 'diserahkan', 'belum_kembali', 'batal'])->default('belum_kembali');
            $table->timestamp('tanggal_kembali')->nullable();
            $table->timestamp('tanggal_pinjam_diserahkan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_tools');
    }
};
