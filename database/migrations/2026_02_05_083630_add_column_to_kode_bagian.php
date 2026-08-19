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
        Schema::table('kode_bagian_spm', function (Blueprint $table) {
            $table->foreignId('departemen_id')->nullable()->constrained('departemens');
            $table->foreignId('bagian_id')->nullable()->constrained('bagians');
            $table->foreignId('unit_id')->nullable()->constrained('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kode_bagian', function (Blueprint $table) {
            //
        });
    }
};
