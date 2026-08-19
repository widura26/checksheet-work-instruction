<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offline_requests', function (Blueprint $table) {
            $table->id();
            $table->string('client_request_id', 64)->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('method', 10);
            $table->string('url', 1000);
            $table->integer('status_code')->default(200);
            $table->string('redirect_url', 1000)->nullable();
            $table->text('response_body')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_requests');
    }
};