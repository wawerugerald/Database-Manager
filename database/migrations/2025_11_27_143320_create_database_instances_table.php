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
        Schema::create('database_instances', function (Blueprint $table) {
           $table->id();
            $table->string('name'); // user friendly name
            $table->string('type'); // mysql | postgres | mongodb
            $table->string('project'); // docker compose project name (unique)
            $table->string('compose_path'); // path to docker-compose.yml
            $table->json('env')->nullable(); // env variables to write to compose if needed
            $table->string('status')->default('stopped'); // running | stopped | starting | stopping | unknown
            $table->text('last_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_instances');
    }
};
