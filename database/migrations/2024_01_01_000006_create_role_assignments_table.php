<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_assignments', function (Blueprint $table) {
            if (config('roles.use_uuids')){
                 $table->uuid('id')->primary();
                 $table->foreignUuid('role_id')->constrained()->onDelete('cascade');
            } else {
                $table->id();
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
            }
            $table->uuidMorphs('assignable');
            $table->nullableUuidMorphs('context');
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
