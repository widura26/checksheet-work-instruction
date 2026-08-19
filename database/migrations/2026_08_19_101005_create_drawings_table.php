<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drawings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->string('nogbr')->unique();
            $table->string('nama')->nullable();
            $table->date('tgl')->nullable();
            $table->string('rev')->nullable();
            $table->bigInteger('detail')->nullable();
            $table->bigInteger('jml_md')->nullable();
            $table->bigInteger('jml_wi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drawings');
    }
};
