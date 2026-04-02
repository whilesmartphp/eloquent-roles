<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('assignable');
            $table->foreignUuid('role_id')->constrained()->onDelete('cascade');
            $table->nullableMorphs('context');
            $table->timestamps();

            $table->unique(
                ['assignable_type', 'assignable_id', 'role_id', 'context_type', 'context_id'],
                'role_assignment_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_assignments');
    }
};
