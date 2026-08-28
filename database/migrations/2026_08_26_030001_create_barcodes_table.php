<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cutting_plan_id')->nullable()->index();
            $table->uuid('part_id')->nullable()->unique();
            $table->string('wbs')->nullable();
            $table->string('lot')->nullable()->index();
            $table->string('part_no')->nullable()->index();
            $table->string('partno1')->nullable();
            $table->string('PR')->nullable();
            $table->string('no_konv')->nullable()->unique();
            $table->date('tgl')->nullable();
            $table->string('status')->nullable();
            $table->string('bil')->nullable();
            $table->uuid('wi_trn_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->date('tgl_print')->nullable();
            $table->index(['part_no', 'PR']);
            $table->timestamps();

            $table->foreign('cutting_plan_id')->references('id')->on('cutting_plans')->nullOnDelete();
            $table->foreign('part_id')->references('id')->on('parts')->nullOnDelete();
            $table->foreign('wi_trn_id')->references('id')->on('work_instructions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('barcode_details', function (Blueprint $table) {
            $table->foreign('barcode_id')->references('id')->on('barcodes')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('barcode_starts', function (Blueprint $table) {
            $table->foreign('barcode_id')->references('id')->on('barcodes')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('work_instructions', function (Blueprint $table) {
            $table->foreign('barcode_id')->references('id')->on('barcodes')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_instructions', function (Blueprint $table) {
            $table->dropForeign(['barcode_id']);
        });

        Schema::table('barcode_starts', function (Blueprint $table) {
            $table->dropForeign(['barcode_id']);
        });

        Schema::table('barcode_details', function (Blueprint $table) {
            $table->dropForeign(['barcode_id']);
        });

        Schema::dropIfExists('barcodes');
    }
};
