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
        Schema::table('material_transfer_postings', function (Blueprint $table) {
            $table->timestamp('canceled_at')->nullable()->after('created_by');
            $table->text('cancel_reason')->nullable()->after('canceled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
