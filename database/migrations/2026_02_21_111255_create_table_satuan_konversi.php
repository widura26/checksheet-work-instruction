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
        Schema::create('satuan_konversi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_material')->unique();
            $table->string('satuan_sap');
            $table->string('satuan_distribusi');
            $table->string('berat_per_satuan_distribusi');
            $table->decimal('nilai_konversi', 15, 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satuan_konversi');
    }
};
