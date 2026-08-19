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
        Schema::table('pengurangan_tools', function (Blueprint $table) {
            $table->foreignId('detail_kerusakan_tools_id')
                ->nullable()
                ->constrained('detail_kerusakan_tools')
                ->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengurangan_tools', function (Blueprint $table) {
            $table->dropForeign(['detail_kerusakan_tools_id']);
            $table->dropColumn('detail_kerusakan_tools_id');
        });
    }
};
