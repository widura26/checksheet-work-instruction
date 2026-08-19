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
        Schema::table('distribusi_tools', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->after('id');
            $table->uuid('public_id')->after('user_id');
            $table->string('project_id')->after('departemen_id');
            $table->foreign('project_id')
                ->references('project_id')
                ->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusi_tools', function (Blueprint $table) {
            $table->dropColumn('public_id');
        });
    }
};
