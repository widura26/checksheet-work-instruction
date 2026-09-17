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
        Schema::create('material_plannings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wbs_description');
            $table->string('pr_number');
            $table->string('del_indicator')->nullable();
            $table->string('pr_status');
            $table->string('request_date');
            $table->string('pr_delivered_date');
            $table->string('changed_on_date');
            $table->string('material_code');
            $table->string('material_description');
            $table->string('spesification');
            $table->string('requested_quantity');
            $table->string('unit');
            $table->string('ordered_quantity');
            $table->string('po_number');
            $table->string('po_date');
            $table->string('po_delivered_date');
            $table->string('created_by');
            $table->string('request_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_plannings');
    }
};
