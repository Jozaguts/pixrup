<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usage_property_monthlies', function (Blueprint $table): void {
            $table->id();
            $table->string('account_scope_type', 32);
            $table->unsignedBigInteger('account_scope_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('property_id');
            $table->char('period_key', 7);
            $table->string('plan_snapshot', 32);
            $table->string('action_first', 24);
            $table->timestamp('first_action_at');
            $table->timestamps();

            $table->unique(
                ['account_scope_type', 'account_scope_id', 'property_id', 'period_key'],
                'usage_scope_property_period_unique'
            );

            $table->index(
                ['account_scope_type', 'account_scope_id', 'period_key'],
                'usage_scope_period_index'
            );

            $table->index(['user_id', 'period_key'], 'usage_user_period_index');
            $table->index(['plan_snapshot', 'period_key'], 'usage_plan_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_property_monthlies');
    }
};
