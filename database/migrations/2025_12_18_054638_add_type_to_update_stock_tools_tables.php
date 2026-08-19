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
            $table->enum('type', ['tambah_stok', 'input_material'])->after('qty_sesudah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('update_stock_tools_tables', function (Blueprint $table) {
            //
        });
    }
};
