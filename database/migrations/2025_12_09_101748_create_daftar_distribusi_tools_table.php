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
        Schema::create('daftar_distribusi_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusi_tools_id')->constrained('distribusi_tools');
            $table->foreignId('tool_id')->constrained('tools')->onDelete('restrict');
            $table->unsignedInteger('jumlah_distribusi');
            $table->unsignedInteger('qty_kembali')->default(0);
            $table->timestamp('tanggal_kembali')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_distribusi_tools');
    }
};
