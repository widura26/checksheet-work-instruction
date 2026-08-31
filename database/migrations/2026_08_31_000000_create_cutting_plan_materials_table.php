<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create cutting_plan_materials table
        Schema::create('cutting_plan_materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cutting_plan_id');
            $table->uuid('material_id')->nullable();
            $table->string('material_size')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->string('wbs')->nullable();
            $table->timestamps();

            $table->foreign('cutting_plan_id')->references('id')->on('cutting_plans')->onDelete('cascade');
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });

        // 2. Add cutting_plan_material_id column to parts (before dropping old columns)
        Schema::table('parts', function (Blueprint $table) {
            $table->uuid('cutting_plan_material_id')->nullable()->after('id');
        });

        // 3. Migrate data: create cutting_plan_materials records and link parts
        $parts = DB::table('parts')->get();
        foreach ($parts as $part) {
            if ($part->cutting_plan_id) {
                $cpmId = DB::table('cutting_plan_materials')
                    ->where('cutting_plan_id', $part->cutting_plan_id)
                    ->where('material_id', $part->material_id)
                    ->value('id');

                if (!$cpmId) {
                    $cp = DB::table('cutting_plans')->where('id', $part->cutting_plan_id)->first();
                    $cpmId = (string) Str::uuid();
                    DB::table('cutting_plan_materials')->insert([
                        'id' => $cpmId,
                        'cutting_plan_id' => $part->cutting_plan_id,
                        'material_id' => $part->material_id,
                        'material_size' => $cp->material_size ?? null,
                        'quantity' => $cp->quantity ?? null,
                        'wbs' => $cp->wbs ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('parts')
                    ->where('id', $part->id)
                    ->update(['cutting_plan_material_id' => $cpmId]);
            }
        }

        // 4. Clean up parts table: drop old FK columns, make new FK non-nullable
        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign(['cutting_plan_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['material_id']);
            $table->dropColumn(['cutting_plan_id', 'project_id', 'material_id']);
        });

        Schema::table('parts', function (Blueprint $table) {
            $table->uuid('cutting_plan_material_id')->nullable(false)->change();
            $table->foreign('cutting_plan_material_id')->references('id')->on('cutting_plan_materials')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropForeign(['cutting_plan_material_id']);
            $table->dropColumn('cutting_plan_material_id');
            $table->uuid('cutting_plan_id');
            $table->string('project_id')->nullable();
            $table->string('material_id')->nullable();
        });

        Schema::dropIfExists('cutting_plan_materials');
    }
};
