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
        Schema::create('material_transfer_posting_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_transfer_posting_id')
                ->constrained('material_transfer_postings')
                ->cascadeOnDelete()
                ->name('mtp_id_foreign');
            $table->foreignId('from_tool_id')
                ->constrained('tools')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('to_tool_id')
                ->constrained('tools')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->unsignedInteger('returned_qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_transfer_posting_items');
    }
};
