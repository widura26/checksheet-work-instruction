<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('md_headers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_md')->unique();
            $table->string('part_no')->unique();
            $table->string('partno1')->nullable();
            $table->string('maspart')->nullable();
            $table->foreignUuid('drawing_id')->nullable()->index();
            $table->string('nopost')->nullable();
            $table->string('title')->nullable();
            $table->foreignUuid('material_id')->nullable()->index();
            $table->string('type')->nullable();
            $table->decimal('t1', 12, 4)->nullable();
            $table->decimal('h1', 12, 4)->nullable();
            $table->decimal('b1', 12, 4)->nullable();
            $table->decimal('l1', 12, 4)->nullable();
            $table->string('nama_group')->nullable();
            $table->decimal('qa_tc1', 12, 4)->nullable();
            $table->string('qb_mc1')->nullable();
            $table->string('sh1')->nullable();
            $table->string('sh2')->nullable();
            $table->string('sh3')->nullable();
            $table->string('ws1')->nullable();
            $table->string('ws2')->nullable();
            $table->string('ws3')->nullable();
            $table->string('ws4')->nullable();
            $table->string('ws5')->nullable();
            $table->string('ws6')->nullable();
            $table->string('ws7')->nullable();
            $table->string('ws8')->nullable();
            $table->text('des')->nullable();
            $table->date('tgl')->nullable();
            $table->string('rev')->nullable();
            $table->string('mdh_md2')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('md_headers');
    }
};
