<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spm_ims_orgs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_spm_ims_org')->unique();
            $table->foreignUuid('spm_ims_id')->nullable()->index();
            $table->foreignId('diminta_id')->nullable()->index();
            $table->foreignId('mengetahui_id')->nullable()->index();
            $table->foreignId('diterima_id')->nullable()->index();
            $table->date('tgl_spm_ims_org')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spm_ims_orgs');
    }
};
