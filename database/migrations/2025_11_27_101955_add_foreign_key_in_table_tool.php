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
        Schema::table('tools', function (Blueprint $table) {
            $table->foreignId('posisi_ct_id')->nullable()->constrained('posisi__c_t_s')->onDelete('set null');
            $table->unique(['kode_material', 'posisi_ct_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->dropForeign(['posisi_ct_id']);
            $table->dropUnique(['kode_material', 'posisi_ct_id']);
        });
    }
};
