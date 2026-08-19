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
        Schema::create('material_transfer_postings', function (Blueprint $table) {
            $table->id();
            $table->uuid('transfer_code')->unique();
            $table->string('from_project_id', 50);
            $table->foreign('from_project_id')
                ->references('project_id')
                ->on('projects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('to_project_id', 50);
            $table->foreign('to_project_id')
                ->references('project_id')
                ->on('projects')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('from_posisi_ct_id')
                ->nullable()
                ->constrained('posisi__c_t_s')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('to_posisi_ct_id')
                ->nullable()
                ->constrained('posisi__c_t_s')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('transfer_date');
            $table->enum('status', [
                'ON_LOAN',
                'PARTIAL_RETURN',
                'RETURNED',
                'CANCELED',
            ])->default('ON_LOAN');
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_transfer_postings');
    }
};
