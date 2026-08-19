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
        Schema::create('detail_daftar_distribusi_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daftar_distribusi_tool_id')->constrained('daftar_distribusi_tools');
            $table->unsignedInteger('jumlah')->default(0);
            $table->string('kondisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_daftar_distribusi_tools');
    }
};
