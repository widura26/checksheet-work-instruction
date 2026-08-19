<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_keretas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('jenis_kereta')->unique();
            $table->text('ket_jenis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_keretas');
    }
};
