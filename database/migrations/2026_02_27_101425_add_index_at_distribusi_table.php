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
        Schema::table('distribusi_tools', function (Blueprint $table) {
            $table->index('unit_id');
            $table->index('bagian_id');
            $table->index('departemen_id');
            $table->index('tanggal_pinjam_diserahkan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusi_tools', function (Blueprint $table) {
            $table->dropIndex(['unit_id']);
            $table->dropIndex(['bagian_id']);
            $table->dropIndex(['departemen_id']);
            $table->dropIndex(['tanggal_pinjam_diserahkan']);
        });
    }
};
