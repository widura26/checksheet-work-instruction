<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('okas', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('drawings', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('mesins', function (Blueprint $table) {
            $table->foreign('process_id')->references('id')->on('processes')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('md_headers', function (Blueprint $table) {
            $table->foreign('drawing_id')->references('id')->on('drawings')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('wi_trn_bpms', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('exps', function (Blueprint $table) {
            $table->foreign('cp_detail_id')->references('id')->on('cp_details')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('pengirim_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('penerima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('cp_details', function (Blueprint $table) {
            $table->foreign('exp_id')->references('id')->on('exps')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('exp_cps', function (Blueprint $table) {
            $table->foreign('cp_detail_id')->references('id')->on('cp_details')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('pengirim_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('penerima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('exp_orgs', function (Blueprint $table) {
            $table->foreign('exp_id')->references('id')->on('exps')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('bpms', function (Blueprint $table) {
            $table->foreign('material_id')->references('id')->on('materials')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('work_instruction_id')->references('id')->on('work_instructions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('bpm_cps', function (Blueprint $table) {
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('cp_detail_id')->references('id')->on('cp_details')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('bpm_ims', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('wi_trn_id')->references('id')->on('work_instructions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('con_fab_ims', function (Blueprint $table) {
            $table->foreign('mesin_id')->references('id')->on('mesins')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('con_fin_ims', function (Blueprint $table) {
            $table->foreign('mesin_id')->references('id')->on('mesins')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('gases', function (Blueprint $table) {
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('grafik_md', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('ims_bpms', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('wi_trn_id')->references('id')->on('work_instructions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('ims_spms', function (Blueprint $table) {
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('wi_trn_id')->references('id')->on('work_instructions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('lap_wi', function (Blueprint $table) {
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('md_gambars', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('manufacture_drawings', function (Blueprint $table) {
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('memos', function (Blueprint $table) {
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('memo_masuks', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('memo_proses_masuks', function (Blueprint $table) {
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('ncrs', function (Blueprint $table) {
            $table->foreign('piecepart_datang_id')->references('id')->on('piecepart_datangs')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('pieceparts', function (Blueprint $table) {
            $table->foreign('piecepart_datang_id')->references('id')->on('piecepart_datangs')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('piecepart_datangs', function (Blueprint $table) {
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('pipts', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('exp_id')->references('id')->on('exps')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('self_checks', function (Blueprint $table) {
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('spm_ims', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('spm_ims_org_id')->references('id')->on('spm_ims_orgs')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('cp_detail_id')->references('id')->on('cp_details')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('wi_trn_id')->references('id')->on('work_instructions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('spm_ims_orgs', function (Blueprint $table) {
            $table->foreign('spm_ims_id')->references('id')->on('spm_ims')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('diminta_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('mengetahui_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('diterima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('spm_orgs', function (Blueprint $table) {
            $table->foreign('diminta_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('mengetahui_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('diterima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('spm_ppcbs', function (Blueprint $table) {
            $table->foreign('cp_detail_id')->references('id')->on('cp_details')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('pengirim_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('mengetahui_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('penerima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('sttp_dalmats', function (Blueprint $table) {
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('sttp_dalmat_datangs', function (Blueprint $table) {
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('sttp_dalmat_datang_orgs', function (Blueprint $table) {
            $table->foreign('sttp_dalmat_datang_id')->references('id')->on('sttp_dalmat_datangs')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('diminta_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('mengetahui_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('diterima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('sttpps', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('pengirim_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('penerima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('sttpp_fabs', function (Blueprint $table) {
            $table->foreign('sttpp_id')->references('id')->on('sttpps')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('jenis_kereta_id')->references('id')->on('jenis_keretas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('pengirim_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('penerima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('tool_fabs', function (Blueprint $table) {
            $table->foreign('mesin_id')->references('id')->on('mesins')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('tool_fabs_ims', function (Blueprint $table) {
            $table->foreign('mesin_id')->references('id')->on('mesins')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('tool_fins', function (Blueprint $table) {
            $table->foreign('mesin_id')->references('id')->on('mesins')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('tool_fins_ims', function (Blueprint $table) {
            $table->foreign('mesin_id')->references('id')->on('mesins')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('wi_finishes', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('wi_trn_id')->references('id')->on('work_instructions')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('jenis_kereta_id')->references('id')->on('jenis_keretas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('sttpp_id')->references('id')->on('sttpps')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('pengirim_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('penerima_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('barcode_details', function (Blueprint $table) {
            $table->foreign('process_id')->references('id')->on('processes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('mesin_id')->references('id')->on('mesins')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('barcode_starts', function (Blueprint $table) {
            $table->foreign('process_id')->references('id')->on('processes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('mesin_id')->references('id')->on('mesins')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('bobot_parts', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('md_header_id')->references('id')->on('md_headers')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('ct_plants', function (Blueprint $table) {
            $table->foreign('oka_id')->references('id')->on('okas')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('cp_detail_id')->references('id')->on('cp_details')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('wi_trn_bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('jenis_kereta_id')->references('id')->on('jenis_keretas')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('work_instructions', function (Blueprint $table) {
            $table->foreign('cutting_plan_id')->references('id')->on('cutting_plans')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('cutting_plans', function (Blueprint $table) {
            $table->foreign('bpm_id')->references('id')->on('bpms')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('material_id')->references('id')->on('materials')->cascadeOnUpdate()->restrictOnDelete();
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
            $table->dropForeign(['spm_ims_id']);
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['exp_id']);
            $table->dropForeign(['user_cp_id']);
            $table->dropForeign(['user_print_id']);
            $table->dropForeign(['parent_id']);
        });

        Schema::table('ct_plants', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['cp_detail_id']);
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['jenis_kereta_id']);
        });

        Schema::table('bobot_parts', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['md_header_id']);
        });

        Schema::table('barcode_starts', function (Blueprint $table) {
            $table->dropForeign(['process_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('barcode_details', function (Blueprint $table) {
            $table->dropForeign(['process_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('wi_finishes', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['wi_trn_id']);
            $table->dropForeign(['jenis_kereta_id']);
            $table->dropForeign(['sttpp_id']);
            $table->dropForeign(['pengirim_id']);
            $table->dropForeign(['penerima_id']);
        });

        Schema::table('tool_fins_ims', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('tool_fins', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('tool_fabs_ims', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
            $table->dropForeign(['user_id']);
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

        Schema::table('sttpps', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['pengirim_id']);
            $table->dropForeign(['penerima_id']);
        });

        Schema::table('sttp_dalmat_datang_orgs', function (Blueprint $table) {
            $table->dropForeign(['sttp_dalmat_datang_id']);
            $table->dropForeign(['diminta_id']);
            $table->dropForeign(['mengetahui_id']);
            $table->dropForeign(['diterima_id']);
        });

        Schema::table('sttp_dalmat_datangs', function (Blueprint $table) {
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
        });

        Schema::table('sttp_dalmats', function (Blueprint $table) {
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
        });

        Schema::table('spm_ppcbs', function (Blueprint $table) {
            $table->dropForeign(['cp_detail_id']);
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['pengirim_id']);
            $table->dropForeign(['mengetahui_id']);
            $table->dropForeign(['penerima_id']);
        });

        Schema::table('spm_orgs', function (Blueprint $table) {
            $table->dropForeign(['diminta_id']);
            $table->dropForeign(['mengetahui_id']);
            $table->dropForeign(['diterima_id']);
        });

        Schema::table('spm_ims_orgs', function (Blueprint $table) {
            $table->dropForeign(['spm_ims_id']);
            $table->dropForeign(['diminta_id']);
            $table->dropForeign(['mengetahui_id']);
            $table->dropForeign(['diterima_id']);
        });

        Schema::table('spm_ims', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['spm_ims_org_id']);
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['cp_detail_id']);
            $table->dropForeign(['wi_trn_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('self_checks', function (Blueprint $table) {
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
        });

        Schema::table('pipts', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['exp_id']);
        });

        Schema::table('piecepart_datangs', function (Blueprint $table) {
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
        });

        Schema::table('pieceparts', function (Blueprint $table) {
            $table->dropForeign(['piecepart_datang_id']);
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['oka_id']);
        });

        Schema::table('ncrs', function (Blueprint $table) {
            $table->dropForeign(['piecepart_datang_id']);
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['oka_id']);
        });

        Schema::table('memo_proses_masuks', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('memo_masuks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('memos', function (Blueprint $table) {
            $table->dropForeign(['md_header_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['oka_id']);
        });

        Schema::table('manufacture_drawings', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['user_id']);
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

        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
        });

        Schema::table('ims_spms', function (Blueprint $table) {
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['wi_trn_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('ims_bpms', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['wi_trn_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('grafik_md', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('gases', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('con_fin_ims', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('con_fab_ims', function (Blueprint $table) {
            $table->dropForeign(['mesin_id']);
        });

        Schema::table('bpm_ims', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['wi_trn_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('bpm_cps', function (Blueprint $table) {
            $table->dropForeign(['bpm_id']);
            $table->dropForeign(['cp_detail_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('bpms', function (Blueprint $table) {
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['material_id']);
            $table->dropForeign(['wi_trn_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('exp_orgs', function (Blueprint $table) {
            $table->dropForeign(['exp_id']);
        });

        Schema::table('exp_cps', function (Blueprint $table) {
            $table->dropForeign(['cp_detail_id']);
            $table->dropForeign(['oka_id']);
            $table->dropForeign(['pengirim_id']);
            $table->dropForeign(['penerima_id']);
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

        Schema::table('wi_trn_bpms', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('md_headers', function (Blueprint $table) {
            $table->dropForeign(['drawing_id']);
            $table->dropForeign(['material_id']);
        });

        Schema::table('mesins', function (Blueprint $table) {
            $table->dropForeign(['process_id']);
        });

        Schema::table('drawings', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('okas', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });
    }
};
