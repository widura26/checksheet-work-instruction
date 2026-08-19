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
        Schema::table('material_transfer_history_items', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->after('qty_returned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_transfer_history_items', function (Blueprint $table) {
            //
        });
    }
};
