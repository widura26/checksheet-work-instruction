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
        Schema::create('tool_project_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')->constrained('tools')->onDelete('cascade');
            $table->foreignId('posisi_ct_id')->nullable()->constrained('posisi__c_t_s')->onDelete('cascade');
            $table->string('project_id');
            $table->unsignedInteger('qty')->default(0);
            $table->timestamps(); // Tambahkan timestamps untuk tracking

            // Foreign key constraints
            $table->foreign('project_id')->references('project_id')->on('projects');

            // Composite unique constraint sesuai requirement
            $table->unique(['tool_id', 'posisi_ct_id', 'project_id'], 'unique_tool_position_project');

            // Index untuk performance query
            $table->index(['tool_id', 'posisi_ct_id'], 'idx_tool_position');
            $table->index('project_id', 'idx_project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_project_stocks');
    }
};
