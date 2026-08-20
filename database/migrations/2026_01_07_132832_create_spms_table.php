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
        Schema::create('spms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('project_id', 50);
            $table->foreign('project_id')
                ->references('id')
                ->on('projects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('departemen_id')->nullable()->constrained('departemens');
            $table->foreignId('bagian_id')->nullable()->constrained('bagians');
            $table->foreignId('unit_id')->nullable()->constrained('units');

            $table->string('no_spm');
            $table->string('kepada');
            $table->timestamp('tanggal_spm');
            $table->enum('destinasi', ['ct', 'workshop']);
            $table->enum('status', [
                'WAITING',
                'COMPLETED',
                'CANCELED'
            ])->default('WAITING');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spms');
    }
};
