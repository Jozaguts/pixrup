<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'plan_tier')) {
                $table->string('plan_tier')->default('PRICE_STARTER')->after('plan');
            }
            if (!Schema::hasColumn('users', 'used_docs')) {
                $table->unsignedInteger('used_docs')->default(0)->after('usage_count');
            }
            if (!Schema::hasColumn('users', 'used_renders')) {
                $table->unsignedInteger('used_renders')->default(0)->after('used_docs');
            }
        });

        // Backfill (ajusta el mapping según tu realidad)
        DB::table('users')->whereNull('plan_tier')->orWhere('plan_tier', '')->update([
            'plan_tier' => DB::raw("
                CASE
                    WHEN plan IN ('enterprise') THEN 'PRICE_ENTERPRISE'
                    WHEN plan IN ('business') THEN 'PRICE_PRO'
                    WHEN plan IN ('pro','professional') THEN 'PRICE_STARTER'
                    ELSE 'PRICE_STARTER'
                END
            "),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'used_renders')) $table->dropColumn('used_renders');
            if (Schema::hasColumn('users', 'used_docs')) $table->dropColumn('used_docs');
            if (Schema::hasColumn('users', 'plan_tier')) $table->dropColumn('plan_tier');
        });
    }
};
