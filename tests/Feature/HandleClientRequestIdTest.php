<?php

namespace Tests\Feature;

use Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase as BaseTestCase;

class HandleClientRequestIdTest extends BaseTestCase
{
    public function test_duplicate_client_request_id_is_not_processed_twice(): void
    {
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Schema::create('offline_requests', function (Blueprint $table) {
            $table->id();
            $table->string('client_request_id', 64)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method', 10);
            $table->string('url', 1000);
            $table->integer('status_code')->default(200);
            $table->string('redirect_url', 1000)->nullable();
            $table->text('response_body')->nullable();
            $table->timestamps();
        });

        $count = 0;
        Route::post('/test-queue', function () use (&$count) {
            $count++;
            return redirect('/done');
        })->middleware('client.request.id');

        $this->withHeaders(['X-Client-Request-Id' => 'abc-123'])->post('/test-queue');
        $this->withHeaders(['X-Client-Request-Id' => 'abc-123'])->post('/test-queue');

        $this->assertEquals(1, $count);
    }

    public function test_different_client_request_ids_are_processed_independently(): void
    {
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Schema::create('offline_requests', function (Blueprint $table) {
            $table->id();
            $table->string('client_request_id', 64)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('method', 10);
            $table->string('url', 1000);
            $table->integer('status_code')->default(200);
            $table->string('redirect_url', 1000)->nullable();
            $table->text('response_body')->nullable();
            $table->timestamps();
        });

        $count = 0;
        Route::post('/test-queue', function () use (&$count) {
            $count++;
            return redirect('/done');
        })->middleware('client.request.id');

        $this->withHeaders(['X-Client-Request-Id' => 'abc-1'])->post('/test-queue');
        $this->withHeaders(['X-Client-Request-Id' => 'abc-2'])->post('/test-queue');

        $this->assertEquals(2, $count);
    }
}
