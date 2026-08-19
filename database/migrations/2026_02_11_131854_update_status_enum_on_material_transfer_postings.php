<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE material_transfer_postings
            MODIFY COLUMN status
            ENUM(
                'ON_LOAN',
                'PARTIAL_RETURN',
                'RETURNED',
                'CANCELED',
                'WRITTEN_OFF'
            )
            NOT NULL DEFAULT 'ON_LOAN'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
