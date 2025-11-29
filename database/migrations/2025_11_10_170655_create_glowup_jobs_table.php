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
        Schema::create('glowup_jobs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->uuid('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->string('room_type', 40);
            $table->string('style', 40);
            $table->string('before_url');
            $table->string('after_url')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('usage_recorded_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('glowup_jobs');
    }
};
