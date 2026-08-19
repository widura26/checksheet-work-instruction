<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcode_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('no_konv')->index();
            $table->foreignUuid('barcode_id')->nullable()->index();
            $table->string('proses')->nullable();
            $table->foreignUuid('process_id')->nullable()->index();
            $table->dateTime('tgl')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->bigInteger('jml_org')->nullable();
            $table->string('user_brcode')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('kode_mesin')->nullable();
            $table->foreignUuid('mesin_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcode_details');
    }
};
