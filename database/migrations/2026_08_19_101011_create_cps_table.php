<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_cp')->nullable()->index();
            $table->string('no_cp')->nullable()->unique();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('mat')->nullable();
            $table->string('spec_mat')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->string('mat_sisa')->nullable();
            $table->foreignUuid('exp_id')->nullable()->index();
            $table->string('shop')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('diterima')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->string('grup')->nullable();
            $table->string('lot')->nullable();
            $table->bigInteger('qty_lot')->nullable();
            $table->foreignUuid('bpm_cp_id')->nullable()->index();
            $table->foreignUuid('exp_cp_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cps');
    }
};
