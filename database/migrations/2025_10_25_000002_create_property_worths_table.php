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
        Schema::create('property_worths', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->decimal('value', 12, 2);
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->json('comparables')->nullable();
            $table->json('trend')->nullable();
            $table->string('provider')->default('housecanary');
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'fetched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_worths');
    }
};
