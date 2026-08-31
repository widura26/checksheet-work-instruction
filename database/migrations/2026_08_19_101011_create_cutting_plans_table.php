<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cutting_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_cp')->nullable()->unique();
            $table->foreignUUid('project_id')->nullable()->index();
            $table->foreignUUid('process_id')->nullable()->index();
            $table->string('sketch')->nullable();
            $table->string('special_instruction')->nullable();
            $table->date('date_issued')->nullable();
            $table->date('date_revised')->nullable();
            $table->foreignId('prepared_by')->nullable()->index();
            $table->foreignId('approved_by')->nullable()->index();
            $table->foreignId('date_operator')->nullable()->index();
            $table->foreignUuid('status_id')->nullable()->index();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->string('wbs')->nullable();
            $table->string('material_size')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('qty_lot')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutting_plans');
    }
};
