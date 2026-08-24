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
            $table->string('id_cp')->nullable()->index();
            $table->string('no_cp')->nullable()->unique();
            $table->string('mat')->nullable();
            $table->string('spec_mat')->nullable();
            $table->string('mat_sisa')->nullable();
            $table->string('shop')->nullable();
            $table->string('diterima')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->string('grup')->nullable();
            $table->string('lot')->nullable();
            $table->bigInteger('qty_lot')->nullable();
            $table->foreignUUid('material_id')->nullable()->index();
            $table->foreignUUid('process_id')->nullable()->index();
            $table->string('material_size')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->string('wbs')->nullable();
            $table->string('sketch')->nullable();
            $table->string('special_instruction')->nullable();
            $table->date('date_issued')->nullable();
            $table->date('date_revised')->nullable();
            $table->foreignId('prepared_by')->nullable()->index();
            $table->foreignId('approved_by')->nullable()->index();
            $table->foreignId('date_operator')->nullable()->index();
            $table->foreignUUid('project_id')->nullable()->index();
            $table->foreignUUid('bpm_id')->nullable()->index();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->foreignUuid('exp_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->foreignUuid('bpm_cp_id')->nullable()->index();
            $table->foreignUuid('exp_cp_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutting_plans');
    }
};
