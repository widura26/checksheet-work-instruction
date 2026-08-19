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
            $table->string('project_id_dest')->after('project_id')->nullable();
            $table->foreign('project_id_dest')->references('project_id')->on('projects');
            $table->string('keterangan')->after('tanggal_pinjam_diserahkan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusi_tools', function (Blueprint $table) {
            //
        });
    }
};
