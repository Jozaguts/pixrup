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
        Schema::create('pixvision_property_runes', static function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('property_id')->index();

            $table->string('rune_key', 64)->index();
            // ej: condition_score, water_damage_detected
            $table->string('provider',32)->default('pix_worth')->index();

            $table->json('rune_value');
            // número, bool, string, objeto simple

            $table->string('version', 16)->index();
            // v1.0.0

            $table->float('confidence')->nullable();
            // 0–1, opcional

            $table->timestamp('computed_at');

            $table->timestamps();

            $table->unique(['property_id', 'rune_key', 'version', 'provider'], 'unique_property_rune');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vision_property_runes');
    }
};
