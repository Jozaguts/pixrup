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
        Schema::create('pixvision_reports', static function (Blueprint $table) {
            $table->id();
            $table->integer('property_id');
            $table->string('aws_uri');
            $table->date('last_downloaded_at')->nullable();
            $table->date('expires_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pixvision_reports');
    }
};
