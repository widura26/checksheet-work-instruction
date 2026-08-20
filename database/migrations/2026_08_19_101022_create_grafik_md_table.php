<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grafik_md', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->nullable()->index();
            $table->string('g_nama')->nullable();
            $table->string('g_lot')->nullable();
            $table->string('g_md')->nullable();
            $table->string('g_wi')->nullable();
            $table->string('g_tampil')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grafik_md');
    }
};
