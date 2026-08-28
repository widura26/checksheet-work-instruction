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
            $table->foreignUUid('material_id')->nullable()->index();
            $table->foreignUUid('project_id')->nullable()->index();
            $table->string('wbs')->nullable();
            $table->string('material_size')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->foreignUUid('process_id')->nullable()->index();
            $table->string('sketch')->nullable();
            $table->foreignUUid('part_id')->nullable()->index();
            $table->bigInteger('qty_lot')->nullable();
            $table->string('special_instruction')->nullable();
            $table->date('date_issued')->nullable();
            $table->date('date_revised')->nullable();
            $table->foreignId('prepared_by')->nullable()->index();
            $table->foreignId('approved_by')->nullable()->index();
            $table->foreignId('date_operator')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutting_plans');
    }
};
