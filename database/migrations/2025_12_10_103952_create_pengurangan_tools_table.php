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
        Schema::create('pengurangan_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict'); // Removed as per request
            $table->foreignId('tool_id')->constrained('tools')->onDelete('restrict');
            // $table->string('kode_material');
            // $table->foreign('kode_material')
            //     ->references('kode_material')
            //     ->on('tools');
            $table->unsignedInteger('qty_sebelum')->default(0);
            $table->unsignedInteger('qty_dikurangi')->default(0);
            $table->unsignedInteger('qty_sesudah')->default(0);
            $table->text('alasan')->nullable();
            $table->string('document_path');
            $table->timestamp('tanggal_pengurangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurangan_tools');
    }
};
