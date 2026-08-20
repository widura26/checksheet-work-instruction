<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('md_gambars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->nullable()->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('gambar')->nullable();
            $table->boolean('ada_gbr')->nullable()->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('md_gambars');
    }
};