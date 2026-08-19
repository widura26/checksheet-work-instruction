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
        Schema::create('material_transfer_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_transfer_posting_id')
                ->constrained('material_transfer_postings')
                ->cascadeOnDelete()
                ->name('mtp_hist_id_foreign');
            $table->enum('action', [
                'CREATED',
                'TRANSFERRED',
                'RETURNED',
            ]);
            $table->text('notes')->nullable();

            $table->foreignId('user_id')
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
        Schema::dropIfExists('material_transfer_histories');
    }
};
