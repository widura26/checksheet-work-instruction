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
        Schema::create('spm_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spm_id')
                ->constrained('spms');
            $table->foreignId('tool_id')
                ->nullable()
                ->constrained('tools');
            $table->string('kode_material');
            $table->string('deskripsi')->nullable();
            $table->string('spesifikasi')->nullable();
            $table->string('satuan')->nullable();
            $table->enum('kategori', ['tool', 'apd', 'consumable']);
            $table->unsignedInteger('requested_qty')->default(0);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spm_items');
    }
};
