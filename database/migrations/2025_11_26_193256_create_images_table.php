<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_uuid')->index();
            $table->string('name');
            $table->string('uri');                // S3 file path or URL
            $table->unsignedBigInteger('size');  // bytes
            $table->string('mime_type')->nullable();
            $table->string('type','10')->default('logo'); // logo, banner, etc.
            $table->timestamps();
            $table->unique(['user_uuid', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
