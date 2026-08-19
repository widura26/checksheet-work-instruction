<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mdrs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_mdr')->unique();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->date('tgl_mdr')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->text('masalah')->nullable();
            $table->string('lanjut')->nullable();
            $table->string('input')->nullable();
            $table->string('dampak')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdrs');
    }
};