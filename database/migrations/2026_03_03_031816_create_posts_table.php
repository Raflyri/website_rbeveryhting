<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['news', 'article', 'blog'])->default('article')->index();
            $table->text('excerpt')->nullable();
            $table->string('thumbnail')->nullable();          // storage path
            $table->json('blocks')->nullable();               // block builder content
            $table->string('author_name')->default('RBeverything Team');
            $table->text('author_bio')->nullable();
            $table->string('author_avatar')->nullable();      // storage path
            $table->unsignedTinyInteger('reading_time_minutes')->default(5);
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
