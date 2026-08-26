<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cutting_plan_id');
            $table->string('project_id')->nullable();
            $table->string('material_id')->nullable();
            $table->string('part_number');
            $table->string('cutting_size');
            $table->integer('quantity_per_car');
            $table->integer('quantity_per_lot');
            $table->string('part_name');
            $table->string('remark')->nullable();

            $table->foreign('cutting_plan_id')->references('id')->on('cutting_plans')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
