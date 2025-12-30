<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('property_overviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('property_id')
                ->constrained('properties')
                ->cascadeOnDelete();

            $table->string('provider', 40)->default('housecanary');
            $table->string('status', 20)->default('ready');

            $table->boolean('owner_occupied')->nullable();

            $table->boolean('fema_disaster_area')->nullable();
            $table->string('flood_zone')->nullable();
            $table->string('flood_risk')->nullable();

            $table->unsignedSmallInteger('crime_percentile')->nullable();
            $table->string('crime_compare_scope', 30)->nullable();

            $table->string('msa', 10)->nullable();
            $table->string('msa_name')->nullable();

            $table->string('census_tract')->nullable();
            $table->string('block_group')->nullable();

            $table->json('payload');

            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->string('error_code', 80)->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique(['property_id', 'provider']);
            $table->index(['property_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_overviews');
    }
};
