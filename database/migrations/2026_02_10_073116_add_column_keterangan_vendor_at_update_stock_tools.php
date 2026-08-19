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
        Schema::table('update_stock_tools', function (Blueprint $table) {
            $table->string('vendor')->nullable()->after('dokumen_pengambilan_path');
            $table->string('keterangan_vendor')->nullable()->after('vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
