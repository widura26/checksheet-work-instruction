<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_jadwal')->unique();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('group_jadwal')->nullable();
            $table->string('lot')->nullable();
            $table->date('jadwal_wi')->nullable();
            $table->uuid('md2_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
