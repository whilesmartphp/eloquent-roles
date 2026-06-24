<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abilities', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->nullableMorphs('subject');
            $table->morphs('assignable');
            $table->nullableMorphs('context');
            $table->boolean('allowed')->default(true);
            $table->json('conditions')->nullable();
            $table->timestamps();

            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abilities');
    }
};
