<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_instructions', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['jenis_kereta_id']);
            $table->dropForeign(['bpm_cp_id']);
            $table->dropForeign(['cp_id']);
            $table->dropForeign(['memo_id']);
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['exp_id']);
            $table->dropForeign(['user_cp_id']);
            $table->dropForeign(['user_print_id']);
            $table->dropForeign(['parent_id']);
        });

        Schema::table('barcode_starts', function (Blueprint $table) {
            $table->dropForeign(['process_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('tool_fins_ims', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('tool_fins', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('tool_fabs', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('sttpp_fabs', function (Blueprint $table) {
            $table->dropForeign(['sttpp_id']);
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['jenis_kereta_id']);
            $table->dropForeign(['pengirim_id']);
            $table->dropForeign(['penerima_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('memos', function (Blueprint $table) {
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['oka_id']);
        });

        Schema::table('md_gambars', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['md_header_id']);
        });

        Schema::table('lap_wi', function (Blueprint $table) {
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
        });

        Schema::table('con_fin_ims', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('con_fab_ims', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('cutting_plans', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['exp_id']);
            $table->dropForeign(['bpm_cp_id']);
            $table->dropForeign(['exp_cp_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['material_id']);
        });

        Schema::table('cp_details', function (Blueprint $table) {
            $table->dropForeign(['exp_id']);
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('exps', function (Blueprint $table) {
            $table->dropForeign(['cp_detail_id']);
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['pengirim_id']);
            $table->dropForeign(['penerima_id']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
