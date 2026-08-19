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
        Schema::create('material_transfer_history_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_transfer_history_id')
                ->constrained('material_transfer_histories')
                ->cascadeOnDelete()
                ->name('mth_items_history_id_foreign');
            $table->foreignId('tool_id')
                ->constrained('tools')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->unsignedInteger('qty_returned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_transfer_history_items');
    }
};
