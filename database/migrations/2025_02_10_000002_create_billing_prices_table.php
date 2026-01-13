<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('billing_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_product_id')
                ->constrained('billing_products')
                ->cascadeOnDelete();
            $table->string('stripe_price_id')->unique();
            $table->string('stripe_product_id')->index();
            $table->enum('type', ['recurring', 'one_time']);
            $table->char('currency', 3);
            $table->unsignedInteger('unit_amount');
            $table->boolean('active')->default(true);
            $table->string('interval')->nullable();
            $table->integer('interval_count')->nullable();
            $table->integer('trial_period_days')->nullable();
            $table->string('usage_type')->nullable();
            $table->string('billing_scheme')->nullable();
            $table->string('tax_behavior')->nullable();
            $table->timestamp('stripe_created_at')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->index(['billing_product_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_prices');
    }
};
