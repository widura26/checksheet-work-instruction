<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufacture_drawings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('manufacture_drawing_code')->unique();
            $table->foreignUuid('project_id')->nullable()->index();
            $table->date('manufacture_drawing_date')->nullable();
            $table->foreignId('user_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mdrs');
    }
};
