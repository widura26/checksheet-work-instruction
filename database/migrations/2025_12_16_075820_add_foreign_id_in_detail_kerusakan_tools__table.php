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
        Schema::table('detail_kerusakan_tools', function (Blueprint $table) {
            $table->uuid('public_id')->after('id');
            $table->enum('keterangan', ['sudah_diganti', 'belum_diganti'])->default('belum_diganti');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_kerusakan_tools_', function (Blueprint $table) {
            //
        });
    }
};
