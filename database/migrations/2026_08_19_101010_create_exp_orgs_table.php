<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exp_orgs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('exp_id')->nullable()->index();
            $table->string('nama_kirim')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exp_orgs');
    }
};
