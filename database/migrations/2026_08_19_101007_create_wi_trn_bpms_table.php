<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wi_trn_bpms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_wi_trn_bpm')->nullable()->index();
            $table->string('idbarang')->unique();
            $table->string('spec')->nullable();
            $table->text('ket')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wi_trn_bpms');
    }
};
