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
        Schema::create('penggantian_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tool_id')
                ->constrained('tools')
                ->onDelete('restrict');
            $table->foreignId('departemen_id')->constrained();
            $table->foreignId('bagian_id')->constrained();
            $table->foreignId('unit_id')->constrained();
            $table->unsignedInteger('qty_diganti');
            $table->date('tanggal_penggantian')->nullable();
            $table->enum('status', ['open', 'close'])->default('close');
            $table->text('keterangan')->nullable();
            $table->text('file');
            $table->text('file_bukti_penyerahan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggantian_tools');
    }
};
