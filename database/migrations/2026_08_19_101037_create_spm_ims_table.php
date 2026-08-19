<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spm_ims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->foreignUuid('spm_ims_org_id')->nullable()->index();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->foreignUuid('cp_detail_id')->nullable()->index();
            $table->foreignUuid('wi_trn_id')->nullable()->index();
            $table->string('no_spm')->unique();
            $table->string('spec')->nullable();
            $table->bigInteger('jml_bpm')->nullable();
            $table->string('satuan')->nullable();
            $table->bigInteger('jml_spm')->nullable();
            $table->date('tgl_spm')->nullable();
            $table->text('ket_spm')->nullable();
            $table->string('head_numbr')->nullable();
            $table->string('penerbit_bpm')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spm_ims');
    }
};
