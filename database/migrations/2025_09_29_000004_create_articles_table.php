<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->index();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('url')->unique();
            $table->string('image_url')->nullable();
            $table->timestamp('published_at')->index();
            $table->string('language', 10)->nullable()->index();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();
            $table->index(['source_id', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
