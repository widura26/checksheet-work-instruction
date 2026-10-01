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
        Schema::create('sap_datasets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wbs_description')->nullable();
            $table->string('pr_number')->nullable();
            $table->string('del_indicator')->nullable();
            $table->string('pr_status')->nullable();
            $table->string('request_date')->nullable();
            $table->string('pr_delivered_date')->nullable();
            $table->string('changed_on_date')->nullable();
            $table->string('material_code')->nullable();
            $table->text('material_description')->nullable();
            $table->string('spesification')->nullable();
            $table->string('requested_quantity')->nullable();
            $table->string('unit')->nullable();
            $table->string('ordered_quantity')->nullable();
            $table->string('po_number')->nullable();
            $table->string('po_date')->nullable();
            $table->string('po_delivered_date')->nullable();
            $table->string('created_by')->nullable();
            $table->string('request_by')->nullable();
            $table->string('wbs_elem')->nullable();
            $table->string('tracking_no')->nullable();
            $table->string('matl_group')->nullable();
            $table->string('rel')->nullable();
            $table->string('item')->nullable();
            $table->string('pgr')->nullable();
            $table->string('plnt')->nullable();
            $table->string('rel_stat')->nullable();
            $table->enum('status', ['valid', 'invalid'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sap_datasets');
    }
};
