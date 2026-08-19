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
        Schema::create('update_stock_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('tool_id')->constrained('tools')->onDelete('restrict');
            $table->string('project_id');
            $table->foreign('project_id')
                ->references('project_id')
                ->on('projects');
            $table->unsignedInteger('qty_sebelum')->default(0);
            $table->unsignedInteger('qty_update')->default(0);
            $table->unsignedInteger('qty_sesudah')->default(0);
            $table->enum('dokumen_pengambilan', ['bpm', 'memo', 'spm']);
            $table->string('nomor_dokumen');
            $table->string('dokumen_pengambilan_path')->nullable();
            $table->timestamp('tanggal_update');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_stock_tools');
    }
};
