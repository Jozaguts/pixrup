<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'pending_plan_tier')) {
                $table->string('pending_plan_tier')->nullable()->after('plan_tier');
            }
            if (! Schema::hasColumn('users', 'pending_plan_change_at')) {
                $table->timestamp('pending_plan_change_at')->nullable()->after('pending_plan_tier');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'pending_plan_change_at')) {
                $table->dropColumn('pending_plan_change_at');
            }
            if (Schema::hasColumn('users', 'pending_plan_tier')) {
                $table->dropColumn('pending_plan_tier');
            }
        });
    }
};
