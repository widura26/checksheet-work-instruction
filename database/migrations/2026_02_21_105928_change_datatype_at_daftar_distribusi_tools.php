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
            ALTER TABLE daftar_distribusi_tools 
            MODIFY COLUMN jumlah_distribusi
            DECIMAL(15, 4)
            NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE daftar_distribusi_tools 
            MODIFY COLUMN jumlah_distribusi
            INT
            NOT NULL
        ");
    }
};
