<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usesUuids = config('roles.uuids', false);

        // 1. Wipe existing Testbench default tables to avoid the conflict
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');

        // 2. Create the table based on the toggle
        Schema::create('users', function (Blueprint $table) use ($usesUuids) {
            if ($usesUuids) {
                $table->uuid('id')->primary();
            } else {
                $table->id(); // Default integer
            }
            
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};