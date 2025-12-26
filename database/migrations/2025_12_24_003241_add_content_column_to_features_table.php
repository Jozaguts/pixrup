<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('features', static function (Blueprint $table) {
            $table->json('content')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
           $table->dropColumn('content');
        });
    }
};
