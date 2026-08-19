<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sttp_dalmat_datang_orgs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_sttp_org')->unique();
            $table->foreignUuid('sttp_dalmat_datang_id')->nullable()->index();
            $table->foreignId('diminta_id')->nullable()->index();
            $table->foreignId('mengetahui_id')->nullable()->index();
            $table->foreignId('diterima_id')->nullable()->index();
            $table->date('tgl_sttp_datang')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sttp_dalmat_datang_orgs');
    }
};
