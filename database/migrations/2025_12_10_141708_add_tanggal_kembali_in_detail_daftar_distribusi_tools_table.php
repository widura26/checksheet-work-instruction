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
        Schema::table('detail_daftar_distribusi_tools', function (Blueprint $table) {
            $table->timestamp('tanggal_kembali')->nullable()->after('kondisi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_daftar_distribusi_tools', function (Blueprint $table) {
            //
        });
    }
};
