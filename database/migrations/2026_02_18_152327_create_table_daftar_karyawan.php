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
        Schema::create('daftar_karyawans', function (Blueprint $table) {
            $table->string('nip', 50)->primary();
            $table->string('nama');
            $table->foreignId('departemen_id')->nullable()->constrained('departemens');
            $table->foreignId('bagian_id')->nullable()->constrained('bagians');
            $table->foreignId('unit_id')->nullable()->constrained('units');
            $table->string('jabatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_karyawans');
    }
};
