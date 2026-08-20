<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lap_wi', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->foreignUuid('project_id')->nullable()->index();
            $table->date('renc_md_trn')->nullable();
            $table->date('renc_wi_trn')->nullable();
            $table->string('status_peng')->nullable();
            $table->text('ket_status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lap_wi');
    }
};
