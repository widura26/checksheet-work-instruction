<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_mesin')->nullable()->index();
            $table->string('kode_mesin')->unique();
            $table->string('nama')->nullable();
            $table->foreignUuid('process_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesins');
    }
};
