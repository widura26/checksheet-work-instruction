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
            $table->string('bpm_no')->nullable()->unique();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->text('specification')->nullable();
            $table->bigInteger('total')->nullable();
            $table->string('unit')->nullable();
            $table->date('request_date')->nullable();
            $table->foreignUuid('work_instruction_id')->nullable()->index();
            $table->string('bprm_no')->nullable();
            $table->date('brpm_date')->nullable();
            $table->bigInteger('bprm_total')->nullable();
            $table->string('stock')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpms');
    }
};
