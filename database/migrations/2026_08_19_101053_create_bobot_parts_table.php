<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bobot_parts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('oka')->nullable();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('proyek')->nullable();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->string('part_no')->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->decimal('bobot', 12, 4)->nullable();
            $table->string('status')->nullable();
            $table->text('ket')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bobot_parts');
    }
};