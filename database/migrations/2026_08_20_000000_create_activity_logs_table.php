<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // The account that acted. Kept as a nullable reference so removing
            // a staff member never erases what was done while they worked here,
            // with their name copied alongside for the same reason.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('actor_name', 120)->nullable();
            $table->string('actor_role', 20)->nullable();

            $table->string('action', 60);
            $table->string('subject_type', 40)->nullable();
            $table->string('subject_id', 60)->nullable();
            $table->string('description', 255);

            // Whatever else is worth keeping for this action: the amount taken,
            // the status moved from and to, the stock before and after.
            $table->json('properties')->nullable();

            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
