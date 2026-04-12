<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            if (config('roles.use_uuids')) {
                $table->uuid('id')->primary();
                $table->uuid('owner_id')->nullable();

            } else {
                $table->id();
                $table->foreignId('owner_id')->nullable();
            }
            $table->string('title');
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
