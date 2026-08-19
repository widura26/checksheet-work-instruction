<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('oka')->unique();
            $table->foreignUuid('proyek_id')->nullable()->index();
            $table->string('wbs')->nullable();
            $table->text('ket')->nullable();
            $table->bigInteger('total_car')->nullable();
            $table->bigInteger('total_lot')->nullable();
            $table->string('jig')->nullable();
            $table->string('prototype')->nullable();
            $table->string('bogie')->nullable();
            $table->string('ktidur')->nullable();
            $table->bigInteger('total_lot1')->nullable();
            $table->bigInteger('total_lot2')->nullable();
            $table->bigInteger('total_lot3')->nullable();
            $table->bigInteger('total_lot4')->nullable();
            $table->date('tgl_oka')->nullable();
            $table->string('label_proyek')->nullable();
            $table->text('ket_proyek')->nullable();
            $table->boolean('tampil_g')->nullable()->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okas');
    }
};
