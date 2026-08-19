<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_self_check')->unique();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('nama')->nullable();
            $table->string('rev')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->string('qty_lot')->nullable();
            $table->string('qty')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_checks');
    }
};