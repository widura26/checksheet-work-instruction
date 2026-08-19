<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_bpm')->nullable()->unique();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->string('spec')->nullable();
            $table->bigInteger('jumlah')->nullable();
            $table->string('satuan')->nullable();
            $table->date('tgl_permintaan')->nullable();
            $table->text('ket_bpm')->nullable();
            $table->foreignUuid('wi_trn_id')->nullable()->index();
            $table->string('no_bprm')->nullable();
            $table->date('tgl_bprm')->nullable();
            $table->string('head_number')->nullable();
            $table->bigInteger('jml_bprm')->nullable();
            $table->string('stok')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpms');
    }
};
