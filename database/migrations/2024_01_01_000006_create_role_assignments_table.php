<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            if (config('roles.use_uuids')) {
                $table->uuidMorphs('assignable');
            } else {
                $table->morphs('assignable');
            }
            if (config('roles.use_uuids')) {
                $table->nullableUuidMorphs('context');
            } else {
                $table->nullableMorphs('context');
            }
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
