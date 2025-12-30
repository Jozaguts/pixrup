<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('msa_market_pulses', function (Blueprint $table) {
            $table->id();

            $table->string('msa', 10);
            $table->string('msa_name')->nullable();
            $table->string('provider', 40)->default('housecanary');

            $table->decimal('inventory_pressure', 10, 4)->nullable();
            $table->decimal('supply_demand', 10, 4)->nullable();
            $table->decimal('pricing_momentum', 10, 4)->nullable();

            $table->json('payload');

            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->string('status', 20)->default('ready');
            $table->string('error_code', 80)->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique(['msa', 'provider']);
            $table->index(['msa', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('msa_market_pulses');
    }
};
