<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bill_of_material_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('bom_id');
            $table->string('original_name_file');
            $table->string('path_file');
            $table->string('revision_version')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_of_material_revisions');
    }
};
