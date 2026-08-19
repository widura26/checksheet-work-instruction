<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cp_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_cp')->nullable()->index();
            $table->foreignUuid('exp_id')->nullable()->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('rev_cp')->nullable();
            $table->string('cp_no')->nullable()->unique();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('lot')->nullable();
            $table->bigInteger('qty_lot')->nullable();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->string('spek_mat_cp')->nullable();
            $table->string('group')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->string('shop_name')->nullable();
            $table->string('mat_sisa')->nullable();
            $table->date('tgl_cp')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('nama_komp')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cp_details');
    }
};
