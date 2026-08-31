<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_instructions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('work_instruction_number')->unique();
            $table->foreignUuid('cutting_plan_id')->nullable()->index();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->uuid('barcode_id')->nullable()->index();
            $table->string('no_exp')->nullable();
            $table->bigInteger('qty_cp')->nullable();
            $table->text('ket_wi')->nullable();
            $table->string('rev')->nullable();
            $table->date('estimasi')->nullable();
            $table->date('release_date')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wi_trns');
    }
};
