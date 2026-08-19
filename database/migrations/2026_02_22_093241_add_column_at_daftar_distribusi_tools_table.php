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
        Schema::table('daftar_distribusi_tools', function (Blueprint $table) {
            $table->decimal('jumlah_lapangan', 15, 4)->after('jumlah_distribusi')->nullable();
            $table->string('satuan_lapangan')->after('jumlah_lapangan')->nullable();
            $table->decimal('nilai_konversi', 15, 4)->after('jumlah_lapangan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_distribusi_tools', function (Blueprint $table) {
            $table->dropColumn(['jumlah_lapangan', 'satuan_lapangan', 'nilai_konversi']);
        });
    }
};
