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
        Schema::create('spm_items_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spm_item_id')
                ->constrained('spm_items')
                ->cascadeOnDelete();
            $table->foreignId('tool_id')
                ->constrained('tools')
                ->restrictOnDelete();
            $table->foreignId('posisi_ct_id')
                ->constrained('posisi__c_t_s')
                ->restrictOnDelete();
            $table->unsignedInteger('qty');
            $table->foreignId('received_by')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamp('received_at');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spm_items_histories');
    }
};
