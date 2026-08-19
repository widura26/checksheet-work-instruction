<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Livewire\after;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daftar_distribusi_tools', function (Blueprint $table) {
            $table->foreignId('posisi_ct_id')->after('tool_id')->constrained('posisi__c_t_s');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_distribusi_tools', function (Blueprint $table) {
            //
        });
    }
};
