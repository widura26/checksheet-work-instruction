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
        Schema::create('purchase_requisition_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignId('purchase_requisition_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('detail_kerusakan_tools_id')
                ->constrained('detail_kerusakan_tools');
            // 1 tool rusak cuma boleh masuk 1 PR
            $table->unique('detail_kerusakan_tools_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_details');
    }
};
