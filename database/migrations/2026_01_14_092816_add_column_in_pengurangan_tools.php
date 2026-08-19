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
        Schema::table('pengurangan_tools', function (Blueprint $table) {
            $table->foreignId('posisi_ct_id')
                ->nullable()
                ->constrained('posisi__c_t_s')
                ->cascadeOnUpdate()
                ->restrictOnDelete()
                ->after('tool_id');
            $table->string('project_id');
            $table->foreign('project_id')
                ->references('project_id')
                ->on('projects')
                ->cascadeOnUpdate()
                ->restrictOnDelete()
                ->after('posisi_ct_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengurangan_tools', function (Blueprint $table) {
            //
        });
    }
};
