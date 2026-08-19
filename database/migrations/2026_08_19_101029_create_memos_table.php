<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_memo')->unique();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('title')->nullable();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('status_p')->nullable();
            $table->text('ket')->nullable();
            $table->string('status_mach')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};