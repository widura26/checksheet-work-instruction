<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wi_trns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('oka')->index();
            $table->foreignUuid('oka_id')->nullable()->index();
            $table->string('proyek')->index();
            $table->foreignUuid('project_id')->nullable()->index();
            $table->string('wbs')->nullable();
            $table->string('lot')->index();
            $table->string('part_no')->index();
            $table->foreignUuid('md_header_id')->nullable()->index();
            $table->string('partno1')->nullable();
            $table->bigInteger('qty_lot')->nullable();
            $table->date('tgl_trn')->nullable();
            $table->string('no_ctplan')->index();
            $table->foreignUuid('bpm_cp_id')->nullable()->index();
            $table->foreignUuid('cp_id')->nullable()->index();
            $table->foreignUuid('spm_ims_id')->nullable()->index();
            $table->foreignUuid('memo_id')->nullable()->index();
            $table->date('tgl_ctplan')->nullable();
            $table->string('idbarang')->nullable();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->string('no_bpm')->nullable();
            $table->foreignUuid('bpm_id')->nullable()->index();
            $table->date('tgl_bpm')->nullable();
            $table->string('barcode')->nullable();
            $table->foreignUuid('barcode_id')->nullable()->index();
            $table->string('status')->nullable();
            $table->text('ket')->nullable();
            $table->string('jenis_kereta')->nullable();
            $table->foreignUuid('jenis_kereta_id')->nullable()->index();
            $table->string('no_konv')->unique();
            $table->boolean('aproved')->nullable()->default(false);
            $table->boolean('aproved1')->nullable()->default(false);
            $table->boolean('aproved2')->nullable()->default(false);
            $table->string('mat_cp')->nullable();
            $table->string('spek_mat_cp')->nullable();
            $table->string('planer')->nullable();
            $table->string('no_exp')->nullable();
            $table->foreignUuid('exp_id')->nullable()->index();
            $table->bigInteger('qty_cp')->nullable();
            $table->string('mat_sisa')->nullable();
            $table->text('ket_wi')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('rev')->nullable();
            $table->string('ket_mat')->nullable();
            $table->string('status_mat')->nullable();
            $table->string('no_pr')->nullable();
            $table->string('no_po')->nullable();
            $table->string('eta')->nullable();
            $table->bigInteger('qty_car')->nullable();
            $table->string('tinggi')->nullable();
            $table->string('tebal')->nullable();
            $table->string('lebar')->nullable();
            $table->string('panjang')->nullable();
            $table->string('name')->nullable();
            $table->string('user_cp')->nullable();
            $table->foreignId('user_cp_id')->nullable()->index();
            $table->string('user_print')->nullable();
            $table->foreignId('user_print_id')->nullable()->index();
            $table->string('type')->nullable();
            $table->string('group_wi')->nullable();
            $table->string('konv_wi')->nullable();
            $table->foreignUuid('parent_id')->nullable()->index();
            $table->date('target_wi')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wi_trns');
    }
};