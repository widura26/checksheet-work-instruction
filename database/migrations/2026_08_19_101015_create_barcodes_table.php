<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cutting_plan_id')->nullable()->index();
            $table->string('part_id')->nullable()->unique();
            $table->string('wbs')->nullable();
            $table->string('lot')->nullable()->index();
            $table->string('part_no')->nullable()->index();
            $table->string('partno1')->nullable();
            $table->string('PR')->nullable();
            $table->string('no_konv')->nullable()->unique();
            $table->date('tgl')->nullable();
            $table->string('status')->nullable();
            $table->string('bil')->nullable();
            $table->uuid('wi_trn_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->date('tgl_print')->nullable();
            $table->index(['part_no', 'PR']);
            $table->timestamps();

            $table->foreign('part_id')->references('id')->on('parts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};
